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
    // Acciones que realmente importan para el reporte de uso de archivos.
    // Excluimos navegación de páginas, logins y gestión interna.
    const ACCIONES_REPORTE = ['subir', 'descargar', 'ver', 'eliminar', 'restaurar_version'];

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

        $query         = $this->buildQuery($request);
        $preview       = (clone $query)->take(15)->get();
        $totalFiltrado = (clone $query)->count();

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
        $filename = 'reporte_archivos_' . now()->format('Y-m-d_H-i') . '.csv';

        RegistroActividad::registrar('ver', 'sistema', null, 'Exportó reporte de actividad a CSV');

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // BOM UTF-8 para Excel

            fputcsv($handle, [
                'Fecha', 'Hora', 'Usuario', 'Correo electrónico',
                'Empresa', 'Rol', 'Departamento',
                'Acción', 'Nombre del archivo / Detalle', 'Dirección IP',
            ]);

            $etiquetas = [
                'subir'             => 'Subió archivo',
                'descargar'         => 'Descargó archivo',
                'eliminar'          => 'Eliminó archivo',
                'ver'               => 'Visualizó archivo',
                'restaurar_version' => 'Restauró versión',
            ];

            $query->with('usuario.empresa')->chunk(500, function ($registros) use ($handle, $etiquetas) {
                foreach ($registros as $r) {
                    fputcsv($handle, [
                        $r->created_at?->format('d/m/Y')  ?? '',
                        $r->created_at?->format('H:i:s')  ?? '',
                        $r->usuario?->nombre_completo      ?? '(sistema)',
                        $r->usuario?->email                ?? '—',
                        $r->usuario?->empresa?->nombre     ?? '—',
                        $r->usuario?->rol                  ?? '—',
                        $r->usuario?->departamento         ?? '—',
                        $etiquetas[$r->accion]             ?? $r->accion,
                        $r->detalles                       ?? '',
                        $r->ip_address                     ?? '',
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
        $query = RegistroActividad::with('usuario.empresa')
            // Por defecto solo mostramos acciones sobre archivos.
            // Si el usuario filtra por una acción específica se respeta su elección.
            ->when(
                !$request->filled('accion'),
                fn($q) => $q->whereIn('accion', self::ACCIONES_REPORTE)
                             ->where(function ($inner) {
                                 // "ver" solo cuenta cuando el recurso es un archivo,
                                 // no cuando es una carpeta o página del sistema.
                                 $inner->where('accion', '!=', 'ver')
                                       ->orWhere(function ($v) {
                                           $v->where('accion', 'ver')
                                             ->where('recurso', 'archivo');
                                       });
                             })
            );

        if ($request->filled('empresa_id')) {
            $query->whereHas('usuario', fn($u) => $u->where('empresa_id', $request->empresa_id));
        }

        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        return $query->orderBy('created_at', 'desc');
    }
}
