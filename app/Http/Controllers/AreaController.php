<?php

namespace App\Http\Controllers;

use App\Models\Carpeta;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AreaController extends Controller
{
    // ── INDEX ──────────────────────────────────────────────
    public function index(): View
    {
        $usuario = Auth::user();
        $esSupervisor = in_array($usuario->rol, ['Superadmin', 'Aux_QHSE']);

        if ($esSupervisor) {
            // Todas las empresas activas + inactivas para gestión
            $empresas = Empresa::ordenadas()
                ->withCount(['carpetas', 'usuarios'])
                ->with(['carpetas' => fn($q) => $q->whereNull('padre_id')
                    ->withCount('archivos')->take(4)])
                ->get();

            // Estadísticas globales para el panel de Superadmin
            $stats = [
                'total_empresas'    => $empresas->count(),
                'empresas_activas'  => $empresas->where('activo', true)->count(),
                'total_carpetas'    => $empresas->sum('carpetas_count'),
                'total_usuarios'    => $empresas->sum('usuarios_count'),
            ];

            return view('areas.index_admin', compact('empresas', 'stats'));
        }

        // Usuario normal: solo su empresa + corporativo
        $empresas = Empresa::activas()
            ->ordenadas()
            ->where(fn($q) => $q
                ->where('id', $usuario->empresa_id)
                ->orWhere('es_corporativo', true)
            )
            ->withCount(['carpetas', 'usuarios'])
            ->with(['carpetas' => fn($q) => $q->whereNull('padre_id')
                ->withCount('archivos')->take(4)])
            ->get();

        return view('areas.index', compact('empresas'));
    }

    // ── SHOW ───────────────────────────────────────────────
    public function show(Empresa $empresa): View
    {
        $usuario = Auth::user();
        $esSupervisor = in_array($usuario->rol, ['Superadmin', 'Aux_QHSE']);

        // Control de acceso
        if (!$esSupervisor) {
            if (!$empresa->es_corporativo && $empresa->id !== $usuario->empresa_id) {
                abort(403, 'No tienes acceso a esta área.');
            }
        }

        $carpetas = Carpeta::where('empresa_id', $empresa->id)
            ->whereNull('padre_id')
            ->withCount('archivos')
            ->orderBy('nombre')
            ->get();

        $totalArchivos = $carpetas->sum('archivos_count');

        return view('areas.show', compact('empresa', 'carpetas', 'totalArchivos', 'esSupervisor'));
    }
}