<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyScope
{
    /*
     * Asegura que el usuario autenticado solo pueda acceder
     * a recursos de su propia empresa, excepto Superadmin y Aux_QHSE.
    */

    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if (usuario){
            return redirect()->route('login');
        }

        if(in_array($usuario->rol, ['Superadmin', 'Aux_QHSE'])) {
            return $next($request);
        }

        this->verificarParametrosRuta($request, $usuario);

        $request->merge(['_empresa_id' => $usuario->empresa_id]);

        return $next($request);
    }

    private function verificarParametrosRuta(Request $request, $usuario): void
    {
        if ($carpetaId = $request->route('carpeta') ?? $request->route('carpeta_id')) {
            $id = is_object($carpetaId) ? $carpetaId->id : $carpetaId;

            $existe = \App\Models\Carpeta::where('id', $id)
                ->where('empresa_id', $usuario->empresa_id)
                ->withTrashed()
                ->exists();

            if (!$existe) {
                abort(403, 'No tienes acceso a esta carpeta.');
            }
        }

        if ($archivoId = $request->route('archivo') ?? $request->route('archivo_id')) {
            $id = is_object($archivoId) ? $archivoId->id : $archivoId;

            $existe = \App\Models\Archivo::where('archivo_id', $id)
                ->join('carpetas', 'archivos.carpeta_id', '=', 'carpetas.id')
                ->where('carpetas.empresa_id', $usuario->empresa_id)
                ->exists();

            if (!$existe) {
                abort(403, 'No tienes acceso a este archivo.');
            }
        }

        if ($empresaId = $request->route('empresa') ?? $request->route('empresa_id')) {
            $id = is_object($empresaId) ? $empresaId->id : $empresaId;

            if ((int) $id !== (int) $usuario->empresa_id){
                abort(403, 'No tienes acceos a esta empresa');
            }
        }
    }
}