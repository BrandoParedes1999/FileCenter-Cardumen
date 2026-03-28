<?php

namespace App\Policies;

use App\Models\Carpeta;
use App\Models\Usuario;
use Illuminate\Auth\Access\HandlesAuthorization;

class FolderPolicy
{
    use HandlesAuthorization;

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

    public function view(Usuario $usuario, Carpeta $carpeta): bool
    {
        if ($carpeta->empresa_id !== $usuario->empresa_id) {
            return false;
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

    public function createIn(Usuario $usuario, Carpeta $carpetaPadre): bool
    {
        if ($carpetaPadre->empresa_id !== $usuario->empresa_id) {
            return false;
        }

        if (in_array($usuario->rol, ['Admin', 'Gerente'])) {
            return true;
        }

        return $carpetaPadre->usuarioPuedeSubir($usuario);
    }

    public function update(Usuario $usuario, Carpeta $carpeta): bool
    {
        if ($carpeta->empresa_id !== $usuario->empresa_id) {
            return false;
        }

        if (in_array($usuario->rol, ['Admin', 'Gerente'])) {
            return true;
        }

        if ($carpeta->creado_por === $usuario->id) {
            return true;
        }

        return $carpeta->usuarioPuedeEditar($usuario);
    }

    public function delete(Usuario $usuario, Carpeta $carpeta): bool
    {
        if ($carpeta->empresa_id !== $usuario->empresa_id) {
            return false;
        }

        if ($usuario->rol === 'Admin') {
            return true;
        }

        if ($carpeta->creado_por === $usuario->id) {
            return in_array($usuario->rol, ['Gerente', 'Auxiliar']);
        }

        return $carpeta->usuarioPuedeBorrar($usuario);
    }

    public function restore(Usuario $usuario, Carpeta $carpeta): bool
    {
        return $carpeta->empresa_id === $usuario->empresa_id
            && $usuario->rol === 'Admin';
    }

    public function managePermissions(Usuario $usuario, Carpeta $carpeta): bool
    {
        if ($carpeta->empresa_id !== $usuario->empresa_id) {
            return false;
        }

        return in_array($usuario->rol, ['Admin', 'Gerente']);
    }

    /**
     * ¿El usuario puede subir archivos a esta carpeta?
     *
     * Nota importante sobre requiere_aprobacion_subida:
     * Este método devuelve TRUE si el usuario tiene permiso de subir,
     * independientemente de si necesita aprobación. La lógica de
     * "subida con aprobación" se maneja en ArchivoController::store()
     * DESPUÉS de que esta policy autoriza el acceso a la ruta.
     *
     * Es decir: puede_subir = puede intentar subir. Si la carpeta
     * requiere aprobación, el archivo quedará pendiente en vez de
     * publicarse directamente.
     */
    public function uploadTo(Usuario $usuario, Carpeta $carpeta): bool
    {
        if ($carpeta->empresa_id !== $usuario->empresa_id) {
            return false;
        }

        // Carpeta en modo solo_lectura: NADIE puede subir
        // (ni siquiera con permiso explícito, es readonly por diseño)
        if ($carpeta->esSoloLectura()) {
            // Solo Admin y Gerente pueden subir en solo_lectura
            // para administración, pero Auxiliar/Empleado no
            return in_array($usuario->rol, ['Admin', 'Gerente']);
        }

        // Carpeta pública: roles con capacidad de subir
        if ($carpeta->es_publico && in_array($usuario->rol, ['Admin', 'Gerente', 'Auxiliar'])) {
            return true;
        }

        return $carpeta->usuarioPuedeSubir($usuario);
    }

    /**
     * ¿El usuario puede descargar desde esta carpeta?
     * Respeta el modo_acceso de la carpeta.
     */
    public function downloadFrom(Usuario $usuario, Carpeta $carpeta): bool
    {
        if ($carpeta->empresa_id !== $usuario->empresa_id) {
            return false;
        }

        // modo solo_lectura: requiere permiso explícito
        if ($carpeta->esSoloLectura()) {
            $p = $carpeta->permisoEfectivo($usuario);
            return $p && $p->puede_descargar;
        }

        if ($carpeta->es_publico) {
            return true;
        }

        return $carpeta->usuarioPuedeDescargar($usuario);
    }
}