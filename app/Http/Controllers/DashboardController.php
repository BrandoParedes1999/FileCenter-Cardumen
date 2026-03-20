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
        $usuario      = Auth::user();
        $esSuperAdmin = in_array($usuario->rol, ['Superadmin', 'Aux_QHSE']);

        // ── 1. STATS ──────────────────────────────────────────
        $totalUsuarios = $esSuperAdmin
            ? Usuario::where('es_activo', true)->count()
            : Usuario::where('empresa_id', $usuario->empresa_id)->where('es_activo', true)->count();

        $totalEmpresas = $esSuperAdmin
            ? Empresa::where('activo', true)->count()
            : 1;

        $totalArchivos = $esSuperAdmin
            ? Archivo::where('esta_eliminado', false)->count()
            : Archivo::where('esta_eliminado', false)
                ->whereHas('carpeta', fn($q) => $q->where('empresa_id', $usuario->empresa_id))->count();

        $totalCarpetas = $esSuperAdmin
            ? Carpeta::whereNull('deleted_at')->count()
            : Carpeta::where('empresa_id', $usuario->empresa_id)->whereNull('deleted_at')->count();

        // ── 2. EMPRESAS con métricas (gráfica + tarjetas) ─────
        $empresas = Empresa::where('activo', true)
            ->when(!$esSuperAdmin, fn($q) => $q->where('id', $usuario->empresa_id))
            ->orderByDesc('es_corporativo')->orderBy('nombre')->get()
            ->map(function ($emp) {
                $emp->total_archivos = Archivo::where('esta_eliminado', false)
                    ->whereHas('carpeta', fn($q) => $q->where('empresa_id', $emp->id))->count();
                $emp->total_miembros = Usuario::where('empresa_id', $emp->id)->where('es_activo', true)->count();
                return $emp;
            });

        $maxArchivos = $empresas->max('total_archivos') ?: 1;

        // ── 3. ACTIVIDAD RECIENTE ──────────────────────────────
        $actividad = RegistroActividad::with('usuario')
            ->when(!$esSuperAdmin, fn($q) => $q->where('usuario_id', $usuario->id))
            ->whereIn('accion', ['subir','descargar','crear_carpeta','eliminar','restaurar_version'])
            ->latest('created_at')->take(7)->get();

        // ── 4. USUARIOS POR ROL ────────────────────────────────
        $coloresRol = [
            'Superadmin'=>'#7c3aed','Aux_QHSE'=>'#06b6d4','Admin'=>'#4338ca',
            'Gerente'=>'#059669','Auxiliar'=>'#f59e0b','Empleado'=>'#94a3b8',
        ];

        $usuariosPorRol = Usuario::where('es_activo', true)
            ->when(!$esSuperAdmin, fn($q) => $q->where('empresa_id', $usuario->empresa_id))
            ->selectRaw('rol, COUNT(*) as total')->groupBy('rol')
            ->orderByRaw("FIELD(rol,'Superadmin','Aux_QHSE','Admin','Gerente','Auxiliar','Empleado')")
            ->get()->map(fn($r) => tap($r, fn($r) => $r->color = $coloresRol[$r->rol] ?? '#94a3b8'));

        $maxRol = $usuariosPorRol->max('total') ?: 1;

        return view('dashboard', compact(
            'totalUsuarios','totalEmpresas','totalArchivos','totalCarpetas',
            'empresas','maxArchivos','actividad','usuariosPorRol','maxRol'
        ));
    }
}