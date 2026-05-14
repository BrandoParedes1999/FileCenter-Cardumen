<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\RegistroActividad;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteActividadController extends Controller
{
    private function soloSuperadmin(): void
    {
        if (!in_array(Auth::user()->rol, ['Superadmin', 'Aux_QHSE'])) {
            abort(403, 'Solo el Superadmin y Aux_QHSE pueden acceder a los reportes.');
        }
    }

    public function index(Request $request): View
    {
        $this->soloSuperadmin();

        $empresas = Empresa::where('activo', true)
            ->orderByDesc('es_corporativo')
            ->orderBy('nombre')
            ->get();

        $usuarios = Usuario::orderBy('paterno')->orderBy('nombre')
            ->get(['id', 'nombre', 'paterno', 'email', 'rol', 'empresa_id']);

        // Preview: últimos 15 registros con los filtros aplicados
        $query = $this->buildQuery($request);
        $preview = $query->latest('created_at')->take(15)->get();
        $totalFiltrado = $this->buildQuery($request)->count();

        return view('reportes.actividad', compact(
            'empresas', 'usuarios', 'preview', 'totalFiltrado'
        ));
    }

    public function exportar(Request $request): StreamedResponse
    {
        $this->soloSuperadmin();

        $request->validate([
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin'    => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
        ]);

        $query    = $this->buildQuery($request);
        $filename = 'reporte_actividad_' . now()->format('Y-m-d_H-i') . '.csv';

        RegistroActividad::registrar(
            'ver', 'sistema', null,
            'Exportó reporte de actividad a CSV'
        );

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // BOM UTF-8 para que Excel reconozca tildes y ñ correctamente
            fwrite($handle, "\xEF\xBB\xBF");

            // Encabezado
            fputcsv($handle, [
                'Fecha',
                'Hora',
                'Usuario',
                'Correo electrónico',
                'Empresa',
                'Rol',
                'Departamento',
                'Acción',
                'Tipo de recurso',
                'ID del recurso',
                'Detalles',
                'Dirección IP',
            ]);

            // Etiquetas legibles para las acciones
            $etiquetasAccion = [
                'subir'             => 'Subir archivo',
                'descargar'         => 'Descargar archivo',
                'eliminar'          => 'Eliminar archivo',
                'editar'            => 'Editar',
                'crear_carpeta'     => 'Crear carpeta',
                'crear_usuario'     => 'Crear usuario',
                'mover'             => 'Mover',
                'ver'               => 'Visualizar',
                'solicitar_acceso'  => 'Solicitar acceso',
                'aprobar_solicitud' => 'Aprobar solicitud',
                'rechazar_solicitud'=> 'Rechazar solicitud',
                'solicitar_subida'  => 'Solicitar subida',
                'aprobar_subida'    => 'Aprobar subida',
                'rechazar_subida'   => 'Rechazar subida',
                'restaurar_version' => 'Restaurar versión',
                'iniciar_sesion'    => 'Iniciar sesión',
                'cerrar_sesion'     => 'Cerrar sesión',
                'login_fallido'     => 'Intento de login fallido',
                'usuario_bloqueado' => 'Usuario bloqueado',
            ];

            $etiquetasRecurso = [
                'archivo'   => 'Archivo',
                'carpeta'   => 'Carpeta',
                'solicitud' => 'Solicitud',
                'usuario'   => 'Usuario',
                'version'   => 'Versión',
                'sistema'   => 'Sistema',
            ];

            // Procesar en chunks para no saturar memoria con logs masivos
            $query->with('usuario.empresa')->chunk(500, function ($registros) use ($handle, $etiquetasAccion, $etiquetasRecurso) {
                foreach ($registros as $r) {
                    fputcsv($handle, [
                        $r->created_at?->format('d/m/Y')     ?? '',
                        $r->created_at?->format('H:i:s')     ?? '',
                        $r->usuario?->nombre_completo         ?? '(sistema)',
                        $r->usuario?->email                   ?? '—',
                        $r->usuario?->empresa?->nombre        ?? '—',
                        $r->usuario?->rol                     ?? '—',
                        $r->usuario?->departamento            ?? '—',
                        $etiquetasAccion[$r->accion]          ?? $r->accion,
                        $etiquetasRecurso[$r->recurso]        ?? $r->recurso,
                        $r->recurso_id                        ?? '—',
                        $r->detalles                          ?? '',
                        $r->ip_address                        ?? '',
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function buildQuery(Request $request)
    {
        $query = RegistroActividad::with('usuario.empresa');

        // Filtro por empresa del usuario
        if ($request->filled('empresa_id')) {
            $query->whereHas('usuario', fn($u) => $u->where('empresa_id', $request->empresa_id));
        }

        // Filtro por usuario específico
        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        // Filtro por tipo de acción
        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }

        // Filtro por rango de fechas
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        return $query->orderBy('created_at', 'desc');
    }
}
