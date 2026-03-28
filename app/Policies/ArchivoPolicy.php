<?php

namespace App\Policies;

use App\Models\Archivo;
use App\Models\Usuario;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArchivoPolicy
{
    use HandlesAuthorization;

    // Superadmin y Aux_QHSE lo pueden todo
    public function before(Usuario $usuario, string $ability): ?bool
    {
        if (in_array($usuario->rol, ['Superadmin', 'Aux_QHSE'])) {
            return true;
        }
        return null;
    }

    public function viewAny(Usuario $usuario): bool
    {
        return $usuario->es_activo;
    }

    /**
     * Ver detalle del archivo.
     * - Misma empresa siempre puede si tiene puede_leer o carpeta pública.
     * - Cross-empresa: CompanyScope ya validó solicitud aprobada.
     */
    public function view(Usuario $usuario, Archivo $archivo): bool
    {
        $carpeta = $archivo->carpeta;

        // Cross-empresa: CompanyScope ya lo validó, pero la policy
        // solo da acceso si la carpeta es pública de corporativo.
        if ($carpeta->empresa_id !== $usuario->empresa_id) {
            return $carpeta->es_publico
                && ($carpeta->empresa->es_corporativo ?? false);
        }

        if ($carpeta->es_publico) {
            return true;
        }

        if (in_array($usuario->rol, ['Admin', 'Gerente'])) {
            return true;
        }

        return $carpeta->usuarioPuedeLeer($usuario);
    }

    public function create(Usuario $usuario): bool
    {
        return in_array($usuario->rol, ['Admin', 'Gerente', 'Auxiliar']);
    }

    /**
     * Editar metadata (descripción).
     */
    public function update(Usuario $usuario, Archivo $archivo): bool
    {
        $carpeta = $archivo->carpeta;

        if ($carpeta->empresa_id !== $usuario->empresa_id) {
            return false;
        }

        if (in_array($usuario->rol, ['Admin', 'Gerente'])) {
            return true;
        }

        if ($archivo->subido_por === $usuario->id) {
            return true;
        }

        return $carpeta->usuarioPuedeEditar($usuario);
    }

    /**
     * Eliminar archivo.
     */
    public function delete(Usuario $usuario, Archivo $archivo): bool
    {
        $carpeta = $archivo->carpeta;

        if ($carpeta->empresa_id !== $usuario->empresa_id) {
            return false;
        }

        if ($usuario->rol === 'Admin') {
            return true;
        }

        if ($archivo->subido_por === $usuario->id) {
            return in_array($usuario->rol, ['Gerente', 'Auxiliar']);
        }

        return $carpeta->usuarioPuedeBorrar($usuario);
    }

    /**
     * DESCARGAR — lógica completa:
     *
     * 1. Carpeta en modo 'solo_lectura': NADIE descarga sin puede_descargar explícito.
     *    Esto aplica incluso a Admin y Gerente de la empresa.
     *
     * 2. Carpeta de otra empresa: solo si hay solicitud de acceso aprobada
     *    con tipo_acceso = 'Descargar' (CompanyScope ya validó el acceso;
     *    aquí validamos el tipo específico de permiso).
     *
     * 3. Carpeta pública de misma empresa: todos pueden descargar
     *    SALVO que sea modo solo_lectura.
     *
     * 4. Resto: verifica puede_descargar en PermisoCarpeta.
     */
    public function download(Usuario $usuario, Archivo $archivo): bool
    {
        $carpeta = $archivo->carpeta;

        // ── Cross-empresa ─────────────────────────────────────────
        if ($carpeta->empresa_id !== $usuario->empresa_id) {
            // Verificar que la solicitud aprobada permita descarga
            return \App\Models\SolicitudAcceso::where('solicitante_id', $usuario->id)
                ->where(function ($q) use ($archivo, $carpeta) {
                    $q->where('archivo_id', $archivo->id)
                    ->orWhere('carpeta_id', $carpeta->id);
                })
                ->where('status', 'Aprobado')
                ->where('tipo_acceso', 'Descargar')
                ->where(function ($q) {
                    $q->whereNull('caduca_en')
                    ->orWhere('caduca_en', '>', now());
                })
                ->exists();
        }

        // ── Carpeta en modo solo_lectura ──────────────────────────
        // En este modo TODOS los roles (incluido Admin/Gerente)
        // necesitan puede_descargar explícito en PermisoCarpeta.
        if ($carpeta->esSoloLectura()) {
            $p = $carpeta->permisoEfectivo($usuario);
            return $p && $p->puede_descargar;
        }

        // ── Carpeta pública de misma empresa ──────────────────────
        if ($carpeta->es_publico) {
            return true;
        }

        // ── Carpeta privada normal ────────────────────────────────
        // Todos los roles necesitan puede_descargar en PermisoCarpeta.
        return $carpeta->usuarioPuedeDescargar($usuario);
    }

    public function restore(Usuario $usuario, Archivo $archivo): bool
    {
        return $archivo->carpeta->empresa_id === $usuario->empresa_id
            && $usuario->rol === 'Admin';
    }
}