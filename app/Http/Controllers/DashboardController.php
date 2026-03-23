<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
use App\Models\Carpeta;
use App\Models\Empresa;
use App\Models\RegistroActividad;
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

        // Niveles de acceso
        $esAdmin    = in_array($rol, ['Superadmin', 'Aux_QHSE']);   // ve todo el sistema
        $esGestor   = in_array($rol, ['Admin', 'Gerente']);          // ve su empresa
        $esEmpleado = in_array($rol, ['Auxiliar', 'Empleado']);      // ve sus documentos

        // ── 1. STATS ──────────────────────────────────────────────
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
            // Empleado: solo sus archivos accesibles
            $totalUsuarios = null; // no se muestra
            $totalEmpresas = null; // no se muestra
            $totalArchivos = Archivo::where('esta_eliminado', false)
                ->where('subido_por', $usuario->id)->count();
            $totalCarpetas = Carpeta::where('empresa_id', $empresaId)
                ->whereNull('deleted_at')->count(); // carpetas que puede ver
        }

        // ── 2. EMPRESAS para gráfica de barras ───────────────────
        // Solo para Admin/Gestor — Empleado no ve la gráfica
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
            ->whereIn('accion', ['subir','descargar','crear_carpeta','eliminar','restaurar_version'])
            ->latest('created_at')->take(7)->get();

        // ── 4. USUARIOS POR ROL ───────────────────────────────────
        // Solo Admin/Gestor
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

        // ── 5. ACCESOS RÁPIDOS (archivos recientes para Empleado) ─
        $archivosRecientes = collect();
        if ($esEmpleado) {
            $archivosRecientes = Archivo::where('esta_eliminado', false)
                ->whereHas('carpeta', fn($q) => $q->where('empresa_id', $empresaId))
                ->latest('created_at')->take(6)->get();
        }

        // ── 6. SOLICITUDES PENDIENTES (solo Admin/Gestor) ─────────
        $solicitudesPendientes = 0;
        if (!$esEmpleado) {
            try {
                $solicitudesPendientes = \App\Models\SolicitudAcceso::where('estado', 'pendiente')
                    ->when(!$esAdmin, fn($q) => $q->whereHas('carpeta',
                        fn($c) => $c->where('empresa_id', $empresaId)))
                    ->count();
            } catch (\Exception $e) {
                $solicitudesPendientes = 0;
            }
        }

                $stats = [
            [
                'iconSvg' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="#a78bfa"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3z"/></svg>',
                'value' => $totalUsuarios ?? 0,
                'label' => 'Usuarios activos',
                'trendText' => 'en el sistema',
                'iconBg' => 'rgba(124,58,237,0.13)',
            ],
            [
                'iconSvg' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="#2dd4bf"><path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8z"/></svg>',
                'value' => $totalEmpresas ?? 0,
                'label' => 'Áreas activas',
                'trendText' => 'empresas',
                'iconBg' => 'rgba(13,148,136,0.13)',
            ],
            [
                'iconSvg' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="#fbbf24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/></svg>',
                'value' => $totalArchivos ?? 0,
                'label' => 'Total archivos',
                'trendText' => 'activos',
                'iconBg' => 'rgba(217,119,6,0.13)',
            ],
            [
                'iconSvg' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="#60a5fa"><path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8z"/></svg>',
                'value' => $totalCarpetas ?? 0,
                'label' => 'Total carpetas',
                'trendText' => 'en el sistema',
                'iconBg' => 'rgba(29,78,216,0.13)',
            ],
        ];

        return view('dashboard', compact(
            'usuario', 'rol', 'esAdmin', 'esGestor', 'esEmpleado',
            'stats',
            'empresas', 'maxArchivos',
            'actividad', 'archivosRecientes',
            'usuariosPorRol', 'maxRol',
            'solicitudesPendientes'
        ));
    }
}