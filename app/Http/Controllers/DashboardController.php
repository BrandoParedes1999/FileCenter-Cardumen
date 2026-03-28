<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
use App\Models\Carpeta;
use App\Models\Empresa;
use App\Models\RegistroActividad;
use App\Models\SolicitudAcceso;
use App\Models\SolicitudSubida;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $usuario   = Auth::user();
        $rol       = $usuario->rol;
        $empresaId = $usuario->empresa_id;

        $esAdmin    = in_array($rol, ['Superadmin', 'Aux_QHSE']);
        $esGestor   = in_array($rol, ['Admin', 'Gerente']);
        $esEmpleado = in_array($rol, ['Auxiliar', 'Empleado']);

        // ── 1. STATS ─────────────────────────────────────────────
        if ($esAdmin) {
            $totalUsuarios = Usuario::where('es_activo', true)->count();
            $totalEmpresas = Empresa::where('activo', true)->count();
            $totalArchivos = Archivo::where('esta_eliminado', false)->count();
            $totalCarpetas = Carpeta::whereNull('deleted_at')->count();
        } elseif ($esGestor) {
            $totalUsuarios = Usuario::where('empresa_id', $empresaId)->where('es_activo', true)->count();
            $totalEmpresas = 1;
            $totalArchivos = Archivo::where('esta_eliminado', false)
                ->whereHas('carpeta', fn($q) => $q->where('empresa_id', $empresaId))->count();
            $totalCarpetas = Carpeta::where('empresa_id', $empresaId)->whereNull('deleted_at')->count();
        } else {
            $totalUsuarios = null;
            $totalEmpresas = null;
            $totalArchivos = Archivo::where('esta_eliminado', false)
                ->where('subido_por', $usuario->id)->count();
            $totalCarpetas = Carpeta::where('empresa_id', $empresaId)
                ->whereNull('deleted_at')->count();
        }

        // ── 2. EMPRESAS para gráfica ──────────────────────────────
        $empresas    = collect();
        $maxArchivos = 1;

        if (!$esEmpleado) {
            $empresas = Empresa::where('activo', true)
                ->when(!$esAdmin, fn($q) => $q->where('id', $empresaId))
                ->ordenadas()->get()
                ->map(function ($emp) {
                    $emp->total_archivos = Archivo::where('esta_eliminado', false)
                        ->whereHas('carpeta', fn($q) => $q->where('empresa_id', $emp->id))->count();
                    $emp->total_miembros = Usuario::where('empresa_id', $emp->id)
                        ->where('es_activo', true)->count();
                    return $emp;
                });
            $maxArchivos = $empresas->max('total_archivos') ?: 1;
        }

        // ── 3. ACTIVIDAD RECIENTE ─────────────────────────────────
        $actividad = RegistroActividad::with('usuario')
            ->when($esEmpleado, fn($q) => $q->where('usuario_id', $usuario->id))
            ->when($esGestor, fn($q) => $q->whereHas('usuario',
                fn($u) => $u->where('empresa_id', $empresaId)))
            ->whereIn('accion', [
                'subir', 'descargar', 'crear_carpeta', 'eliminar',
                'restaurar_version', 'aprobar_subida', 'rechazar_subida',
            ])
            ->latest('created_at')->take(7)->get();

        // ── 4. USUARIOS POR ROL ───────────────────────────────────
        $usuariosPorRol = collect();
        $maxRol         = 1;

        if (!$esEmpleado) {
            $coloresRol = [
                'Superadmin' => '#7c3aed', 'Aux_QHSE' => '#06b6d4',
                'Admin'      => '#4338ca', 'Gerente'  => '#059669',
                'Auxiliar'   => '#f59e0b', 'Empleado' => '#94a3b8',
            ];
            $usuariosPorRol = Usuario::where('es_activo', true)
                ->when(!$esAdmin, fn($q) => $q->where('empresa_id', $empresaId))
                ->selectRaw('rol, COUNT(*) as total')->groupBy('rol')
                ->orderByRaw("FIELD(rol,'Superadmin','Aux_QHSE','Admin','Gerente','Auxiliar','Empleado')")
                ->get()->map(fn($r) => tap($r, fn($r) => $r->color = $coloresRol[$r->rol] ?? '#94a3b8'));
            $maxRol = $usuariosPorRol->max('total') ?: 1;
        }

        // ── 5. ARCHIVOS RECIENTES (Empleado) ──────────────────────
        $archivosRecientes = collect();
        if ($esEmpleado) {
            $archivosRecientes = Archivo::where('esta_eliminado', false)
                ->whereHas('carpeta', fn($q) => $q->where('empresa_id', $empresaId))
                ->latest('created_at')->take(6)->get();
        }

        // ── 6. SOLICITUDES DE ACCESO PENDIENTES ───────────────────
        $solicitudesPendientes = 0;
        if (!$esEmpleado) {
            try {
                $solicitudesPendientes = SolicitudAcceso::where('status', 'Pendiente')
                    ->when(!$esAdmin, fn($q) => $q->where('empresa_objetivo_id', $empresaId))
                    ->count();
            } catch (\Exception $e) {
                $solicitudesPendientes = 0;
            }
        }

        // ── 7. SOLICITUDES DE SUBIDA PENDIENTES ───────────────────
        // Solo para gestores (Admin/Gerente/Superadmin/Aux_QHSE)
        $solicitudesSubidaPendientes = 0;
        if (!$esEmpleado) {
            try {
                $solicitudesSubidaPendientes = SolicitudSubida::where('status', 'Pendiente')
                    ->when(
                        !$esAdmin,
                        fn($q) => $q->whereHas('carpeta', fn($c) => $c->where('empresa_id', $empresaId))
                    )
                    ->count();
            } catch (\Exception $e) {
                $solicitudesSubidaPendientes = 0;
            }
        }

        return view('dashboard', compact(
            'usuario', 'rol', 'esAdmin', 'esGestor', 'esEmpleado',
            'totalUsuarios', 'totalEmpresas', 'totalArchivos', 'totalCarpetas',
            'empresas', 'maxArchivos',
            'actividad', 'archivosRecientes',
            'usuariosPorRol', 'maxRol',
            'solicitudesPendientes',
            'solicitudesSubidaPendientes'
        ));
    }
}