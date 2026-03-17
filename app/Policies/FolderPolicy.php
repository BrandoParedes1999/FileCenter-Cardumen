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

        return null; // Continúa con el método individual
    }

    // ─────────────────────────────────────────────
    // VER LISTADO DE CARPETAS
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
        // Aislamiento de empresa
        if ($carpeta->empresa_id !== $usuario->empresa_id) {
            return false;
        }

        // Carpeta pública: cualquiera de la empresa puede ver
        if ($carpeta->es_publico) {
            return true;
        }

        // Admins de empresa ven todo dentro de su empresa
        if (in_array($usuario->rol, ['Admin', 'Gerente'])) {
            return true;
        }

        // El resto necesita permiso explícito (con herencia)
        return $carpeta->usuarioPuedeLeer($usuario);
    }

    // ─────────────────────────────────────────────
    // CREAR SUBCARPETA
    // ─────────────────────────────────────────────

    public function create(Usuario $usuario): bool
    {
        // Solo roles con capacidad de organizar
        return in_array($usuario->rol, ['Admin', 'Gerente', 'Auxiliar']);
    }

    /**
     * Crear subcarpeta dentro de una carpeta específica (con herencia).
     * Se llama manualmente: $this->authorize('createIn', $carpetaPadre)
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
    // ─────────────────────────────────────────────

    public function update(Usuario $usuario, Carpeta $carpeta): bool
    {
        if ($carpeta->empresa_id !== $usuario->empresa_id) {
            return false;
        }

        if (in_array($usuario->rol, ['Admin', 'Gerente'])) {
            return true;
        }

        // El creador puede editar su propia carpeta
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

        // Solo Admin puede eliminar cualquier carpeta de su empresa
        if ($usuario->rol === 'Admin') {
            return true;
        }

        // El creador puede eliminar su propia carpeta (si está vacía, lo verifica el controller)
        if ($carpeta->creado_por === $usuario->id) {
            return in_array($usuario->rol, ['Gerente', 'Auxiliar']);
        }

        return $carpeta->usuarioPuedeBorrar($usuario);
    }

    // ─────────────────────────────────────────────
    // RESTAURAR (desde soft delete)
    // ─────────────────────────────────────────────

    public function restore(Usuario $usuario, Carpeta $carpeta): bool
    {
        if ($carpeta->empresa_id !== $usuario->empresa_id) {
            return false;
        }

        return $usuario->rol === 'Admin';
    }

    // ─────────────────────────────────────────────
    // GESTIONAR PERMISOS DE LA CARPETA
    // ─────────────────────────────────────────────

    public function managePermissions(Usuario $usuario, Carpeta $carpeta): bool
    {
        if ($carpeta->empresa_id !== $usuario->empresa_id) {
            return false;
        }

        return in_array($usuario->rol, ['Admin', 'Gerente']);
    }

    // ─────────────────────────────────────────────
    // SUBIR ARCHIVOS EN LA CARPETA
    // ─────────────────────────────────────────────

    public function uploadTo(Usuario $usuario, Carpeta $carpeta): bool
    {
        if ($carpeta->empresa_id !== $usuario->empresa_id) {
            return false;
        }

        if (in_array($usuario->rol, ['Admin', 'Gerente'])) {
            return true;
        }

        return $carpeta->usuarioPuedeSubir($usuario);
    }

    // ─────────────────────────────────────────────
    // DESCARGAR DESDE LA CARPETA
    // ─────────────────────────────────────────────

    public function downloadFrom(Usuario $usuario, Carpeta $carpeta): bool
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

        return $carpeta->usuarioPuedeDescargar($usuario);
    }
}