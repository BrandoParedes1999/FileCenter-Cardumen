<?php

namespace App\Policies;

use App\Models\Archivo;
use App\Models\Usuario;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArchivoPolicy
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

    public function viewAny(Usuario $usuario): bool
    {
        return $usuario->es_activo;
    }

    public function view(Usuario $usuario, Archivo $archivo): bool
    {
        $carpeta = $archivo->carpeta;

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

    public function update(Usuario $usuario, Archivo $archivo): bool
    {
        $carpeta = $archivo->carpeta;

        if ($carpeta->empresa_id !== $usuario->empresa_id) {
            return false;
        }

        if (in_array($usuario->rol, ['Admin', 'Gerente'])) {
            return true;
        }

        // El que subió puede editar metadata
        if ($archivo->subido_por === $usuario->id) {
            return true;
        }

        return $carpeta->usuarioPuedeEditar($usuario);
    }

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

    public function download(Usuario $usuario, Archivo $archivo): bool
    {
        $carpeta = $archivo->carpeta;

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

    public function restore(Usuario $usuario, Archivo $archivo): bool
    {
        return $archivo->carpeta->empresa_id === $usuario->empresa_id
            && $usuario->rol === 'Admin';
    }
}