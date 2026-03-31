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
        // Corporativo → todos pueden ver
        if ($this->esCorporativo($carpeta)) return true;

        if ($carpeta->empresa_id !== $usuario->empresa_id) {
            return false;
        }

        if ($carpeta->es_publico) return true;

        if (in_array($usuario->rol, ['Admin', 'Gerente'])) return true;

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
     * Corporativo: nadie externo puede subir (solo SA/AuxQHSE via before()).
     * El corporativo es de lectura/descarga para el resto de empleados.
     */
    public function uploadTo(Usuario $usuario, Carpeta $carpeta): bool
    {
        // Nadie de otra empresa sube al corporativo
        // (SA/AuxQHSE ya fueron capturados por before())
        if ($this->esCorporativo($carpeta)) {
            return false;
        }

        if ($carpeta->empresa_id !== $usuario->empresa_id) {
            return false;
        }

        // Carpeta en modo solo_lectura: solo Admin y Gerente pueden subir
        if ($carpeta->esSoloLectura()) {
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
     *
     * Corporativo → todos pueden descargar (salvo modo solo_lectura).
     */
    public function downloadFrom(Usuario $usuario, Carpeta $carpeta): bool
    {
        // ── Corporativo ───────────────────────────────────────────
        if ($this->esCorporativo($carpeta)) {
            if ($carpeta->esSoloLectura()) {
                $p = $carpeta->permisoEfectivo($usuario);
                return $p && $p->puede_descargar;
            }
            return true;
        }

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

    // ── Helpers ─────────────────────────────────────────────────

    private function esCorporativo(Carpeta $carpeta): bool
    {
        if ($carpeta->relationLoaded('empresa')) {
            return (bool) $carpeta->empresa?->es_corporativo;
        }

        return \App\Models\Empresa::where('id', $carpeta->empresa_id)
            ->where('es_corporativo', true)
            ->exists();
    }
}