<?php

namespace App\Policies;

use App\Models\Carpeta;
use App\Models\Usuario;
use Illuminate\Auth\Access\HandlesAuthorization;

class FolderPolicy
{
    use HandlesAuthorization;

    // ─────────────────────────────────────────────
    // BEFORE — Superadmin y Aux_QHSE lo pueden todo
    // ─────────────────────────────────────────────

    public function before(Usuario $usuario, string $ability): ?bool
    {
        if (in_array($usuario->rol, ['Superadmin', 'Aux_QHSE'])) {
            return true;
        }
        return null;
    }

    // ─────────────────────────────────────────────
    // VER LISTADO
    // ─────────────────────────────────────────────

    public function viewAny(Usuario $usuario): bool
    {
        return $usuario->es_activo;
    }

    // ─────────────────────────────────────────────
    // VER CARPETA INDIVIDUAL
    // ─────────────────────────────────────────────

    public function view(Usuario $usuario, Carpeta $carpeta): bool
    {
        if ($carpeta->empresa_id !== $usuario->empresa_id) {
            return false;
        }

        if ($carpeta->es_publico) {
            return true;
        }

        // Admin/Gerente ven todo de su empresa (gestores)
        if (in_array($usuario->rol, ['Admin', 'Gerente'])) {
            return true;
        }

        return $carpeta->usuarioPuedeLeer($usuario);
    }

    // ─────────────────────────────────────────────
    // CREAR CARPETA RAÍZ
    // ─────────────────────────────────────────────

    public function create(Usuario $usuario): bool
    {
        return in_array($usuario->rol, ['Admin', 'Gerente', 'Auxiliar']);
    }

    /**
     * Crear subcarpeta dentro de una carpeta específica.
     */
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

    // ─────────────────────────────────────────────
    // ACTUALIZAR (renombrar, cambiar visibilidad)
    // Admin/Gerente pueden gestionar carpetas de su empresa
    // ─────────────────────────────────────────────

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

    // ─────────────────────────────────────────────
    // ELIMINAR
    // ─────────────────────────────────────────────

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

    // ─────────────────────────────────────────────
    // RESTAURAR
    // ─────────────────────────────────────────────

    public function restore(Usuario $usuario, Carpeta $carpeta): bool
    {
        return $carpeta->empresa_id === $usuario->empresa_id
            && $usuario->rol === 'Admin';
    }

    // ─────────────────────────────────────────────
    // GESTIONAR PERMISOS
    // ─────────────────────────────────────────────

    public function managePermissions(Usuario $usuario, Carpeta $carpeta): bool
    {
        if ($carpeta->empresa_id !== $usuario->empresa_id) {
            return false;
        }

        return in_array($usuario->rol, ['Admin', 'Gerente']);
    }

    // ─────────────────────────────────────────────
    // SUBIR ARCHIVOS
    //
    // CAMBIO: Admin y Gerente ahora también necesitan puede_subir
    // para ser consistentes con la regla de PermisoCarpeta estricto.
    // Si necesitas que Admin/Gerente siempre puedan subir sin permiso
    // explícito, cambia la lógica aquí.
    // ─────────────────────────────────────────────

    public function uploadTo(Usuario $usuario, Carpeta $carpeta): bool
    {
        if ($carpeta->empresa_id !== $usuario->empresa_id) {
            return false;
        }

        // Carpeta pública: cualquiera puede subir si tiene rol con capacidad
        if ($carpeta->es_publico && in_array($usuario->rol, ['Admin', 'Gerente', 'Auxiliar'])) {
            return true;
        }

        return $carpeta->usuarioPuedeSubir($usuario);
    }

    // ─────────────────────────────────────────────
    // DESCARGAR DESDE LA CARPETA
    // (misma lógica que ArchivoPolicy::download)
    // ─────────────────────────────────────────────

    public function downloadFrom(Usuario $usuario, Carpeta $carpeta): bool
    {
        if ($carpeta->empresa_id !== $usuario->empresa_id) {
            return false;
        }

        if ($carpeta->es_publico) {
            return true;
        }

        return $carpeta->usuarioPuedeDescargar($usuario);
    }
}