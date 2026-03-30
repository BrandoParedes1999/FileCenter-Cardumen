<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyScope
{
    /*
     * Asegura que el usuario autenticado solo pueda acceder
     * a recursos de su propia empresa, excepto:
     * - Superadmin y Aux_QHSE (acceso total)
     * - Recursos con solicitud de acceso aprobada y vigente (cross-empresa)
    */

    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        // Supervisores: sin restricciones de empresa
        if (in_array($usuario->rol, ['Superadmin', 'Aux_QHSE'])) {
            return $next($request);
        }

        $this->verificarParametrosRuta($request, $usuario);

        $request->merge(['_empresa_id' => $usuario->empresa_id]);

        return $next($request);
    }

    private function verificarParametrosRuta(Request $request, $usuario): void
    {
        // ── CARPETA ──────────────────────────────────────────────────
        if ($carpetaId = $request->route('carpeta') ?? $request->route('carpeta_id')) {
            $id = is_object($carpetaId) ? $carpetaId->id : $carpetaId;

            // Carpeta de la misma empresa → OK
            $esPropiaEmpresa = \App\Models\Carpeta::where('id', $id)
                ->where('empresa_id', $usuario->empresa_id)
                ->withTrashed()
                ->exists();

            if (!$esPropiaEmpresa) {
                // ¿Es carpeta del corporativo? → todos los usuarios activos pueden acceder
                $esCorporativo = \App\Models\Carpeta::where('id', $id)
                    ->whereHas('empresa', fn($q) => $q->where('es_corporativo', true))
                    ->withTrashed()
                    ->exists();

                if ($esCorporativo) {
                    return; // Permitido: corporativo es accesible para todos
                }

                // ¿Tiene solicitud de acceso aprobada y vigente para esta carpeta?
                $tieneAcceso = \App\Models\SolicitudAcceso::where('solicitante_id', $usuario->id)
                    ->where('carpeta_id', $id)
                    ->where('status', 'Aprobado')
                    ->where(function ($q) {
                        $q->whereNull('caduca_en')
                        ->orWhere('caduca_en', '>', now());
                    })
                    ->exists();

                if (!$tieneAcceso) {
                    abort(403, 'No tienes acceso a esta carpeta.');
                }
            }
        }

        // ── ARCHIVO ──────────────────────────────────────────────────
        if ($archivoId = $request->route('archivo') ?? $request->route('archivo_id')) {
            $id = is_object($archivoId) ? $archivoId->id : $archivoId;

            // Archivo de la misma empresa → OK
            $esPropiaEmpresa = \App\Models\Archivo::where('archivos.id', $id)
                ->join('carpetas', 'archivos.carpeta_id', '=', 'carpetas.id')
                ->where('carpetas.empresa_id', $usuario->empresa_id)
                ->exists();

            if (!$esPropiaEmpresa) {
                // ¿Es archivo del corporativo? → permitido
                $esCorporativo = \App\Models\Archivo::where('archivos.id', $id)
                    ->join('carpetas', 'archivos.carpeta_id', '=', 'carpetas.id')
                    ->join('empresas', 'carpetas.empresa_id', '=', 'empresas.id')
                    ->where('empresas.es_corporativo', true)
                    ->exists();

                if ($esCorporativo) {
                    return; // Permitido
                }

                // ¿Tiene solicitud aprobada para este archivo o su carpeta?
                $tieneAcceso = \App\Models\SolicitudAcceso::where('solicitante_id', $usuario->id)
                    ->where(function ($q) use ($id) {
                        $q->where('archivo_id', $id)
                        ->orWhereIn('carpeta_id', function ($sub) use ($id) {
                            $sub->select('carpeta_id')
                                ->from('archivos')
                                ->where('id', $id);
                        });
                    })
                    ->where('status', 'Aprobado')
                    ->where(function ($q) {
                        $q->whereNull('caduca_en')
                        ->orWhere('caduca_en', '>', now());
                    })
                    ->exists();

                if (!$tieneAcceso) {
                    abort(403, 'No tienes acceso a este archivo.');
                }
            }
        }

        // ── EMPRESA ──────────────────────────────────────────────────
        if ($empresaId = $request->route('empresa') ?? $request->route('empresa_id')) {
            $id = is_object($empresaId) ? $empresaId->id : $empresaId;

            if ((int) $id !== (int) $usuario->empresa_id) {
                // Permitir acceso a la empresa corporativa
                $esCorporativo = \App\Models\Empresa::where('id', $id)
                    ->where('es_corporativo', true)
                    ->exists();

                if (!$esCorporativo) {
                    abort(403, 'No tienes acceso a esta empresa.');
                }
            }
        }
    }
}