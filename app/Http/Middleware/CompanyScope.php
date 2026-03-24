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
                // ¿Tiene solicitud aprobada y vigente para esta carpeta?
                $tieneAcceso = \App\Models\SolicitudAcceso::where('solicitante_id', $usuario->id)
                    ->where('carpeta_id', $id)
                    ->where('status', 'Aprobado')
                    ->where(function ($q) {
                        $q->whereNull('caduca_en')
                          ->orWhere('caduca_en', '>', now());
                    })
                    ->exists();

                if (!$tieneAcceso) {
                    // ¿La carpeta pertenece a empresa corporativa y es pública?
                    $esCorpPublica = \App\Models\Carpeta::where('id', $id)
                        ->where('es_publico', true)
                        ->whereHas('empresa', fn($q) => $q->where('es_corporativo', true))
                        ->exists();

                    if (!$esCorpPublica) {
                        abort(403, 'No tienes acceso a esta carpeta.');
                    }
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
                // ¿Tiene solicitud aprobada y vigente para este archivo?
                $tieneAcceso = \App\Models\SolicitudAcceso::where('solicitante_id', $usuario->id)
                    ->where('archivo_id', $id)
                    ->where('status', 'Aprobado')
                    ->where(function ($q) {
                        $q->whereNull('caduca_en')
                          ->orWhere('caduca_en', '>', now());
                    })
                    ->exists();

                // O solicitud aprobada para la carpeta contenedora
                if (!$tieneAcceso) {
                    $carpetaDelArchivo = \App\Models\Archivo::where('archivos.id', $id)
                        ->join('carpetas', 'archivos.carpeta_id', '=', 'carpetas.id')
                        ->value('carpetas.id');

                    if ($carpetaDelArchivo) {
                        $tieneAcceso = \App\Models\SolicitudAcceso::where('solicitante_id', $usuario->id)
                            ->where('carpeta_id', $carpetaDelArchivo)
                            ->where('status', 'Aprobado')
                            ->where(function ($q) {
                                $q->whereNull('caduca_en')
                                  ->orWhere('caduca_en', '>', now());
                            })
                            ->exists();
                    }
                }

                // ¿Es de empresa corporativa y carpeta pública?
                if (!$tieneAcceso) {
                    $esCorpPublica = \App\Models\Archivo::where('archivos.id', $id)
                        ->join('carpetas', 'archivos.carpeta_id', '=', 'carpetas.id')
                        ->join('empresas', 'carpetas.empresa_id', '=', 'empresas.id')
                        ->where('carpetas.es_publico', true)
                        ->where('empresas.es_corporativo', true)
                        ->exists();

                    if (!$esCorpPublica) {
                        abort(403, 'No tienes acceso a este archivo.');
                    }
                }
            }
        }

        // ── EMPRESA ──────────────────────────────────────────────────
        if ($empresaId = $request->route('empresa') ?? $request->route('empresa_id')) {
            $id = is_object($empresaId) ? $empresaId->id : $empresaId;

            if ((int) $id !== (int) $usuario->empresa_id) {
                abort(403, 'No tienes acceso a esta empresa.');
            }
        }
    }
}