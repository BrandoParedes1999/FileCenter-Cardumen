<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
use App\Models\Carpeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BuscadorController extends Controller
{
    /**
     * Mínimo de caracteres para buscar.
     */
    const MIN_CHARS = 2;

    public function index(Request $request): View
    {
        $q       = trim($request->get('q', ''));
        $tipo    = $request->get('tipo', 'todo');   // todo | archivos | carpetas
        $usuario = Auth::user();
        $esAdmin = in_array($usuario->rol, ['Superadmin', 'Aux_QHSE']);

        $archivos = collect();
        $carpetas = collect();
        $valido   = mb_strlen($q) >= self::MIN_CHARS;

        if ($valido) {

            // ── ARCHIVOS ──────────────────────────────────────────
            if ($tipo === 'todo' || $tipo === 'archivos') {
                $qArchivos = Archivo::with(['carpeta.empresa', 'subidoPor'])
                    ->where('esta_eliminado', false)
                    ->where(function ($w) use ($q) {
                        $w->where('nombre_original', 'like', "%{$q}%")
                          ->orWhere('descripcion', 'like', "%{$q}%")
                          ->orWhere('extension', 'like', "%{$q}%");
                    });

                // Limitar por empresa según rol
                if (!$esAdmin) {
                    $qArchivos->whereHas('carpeta', function ($c) use ($usuario) {
                        $c->where(function ($inner) use ($usuario) {
                            $inner->where('empresa_id', $usuario->empresa_id)
                                  ->orWhereHas('empresa', fn($e) => $e->where('es_corporativo', true));
                        });
                    });
                }

                $archivos = $qArchivos->orderBy('nombre_original')
                                      ->limit(30)
                                      ->get()
                                      ->filter(fn($a) => Auth::user()->can('view', $a));
            }

            // ── CARPETAS ──────────────────────────────────────────
            if ($tipo === 'todo' || $tipo === 'carpetas') {
                $qCarpetas = Carpeta::with(['empresa', 'creadoPor'])
                    ->where(function ($w) use ($q) {
                        $w->where('nombre', 'like', "%{$q}%")
                          ->orWhere('path', 'like', "%{$q}%");
                    });

                if (!$esAdmin) {
                    $qCarpetas->where(function ($inner) use ($usuario) {
                        $inner->where('empresa_id', $usuario->empresa_id)
                              ->orWhereHas('empresa', fn($e) => $e->where('es_corporativo', true));
                    });
                }

                $carpetas = $qCarpetas->orderBy('nombre')
                                      ->limit(20)
                                      ->get()
                                      ->filter(fn($c) => $c->usuarioPuedeLeer($usuario));
            }
        }

        return view('buscador.index', compact('q', 'tipo', 'archivos', 'carpetas', 'valido'));
    }
}