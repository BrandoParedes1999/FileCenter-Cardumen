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

    /**
     * Ver detalle del archivo.
     * Admin/Gerente ven todo de su empresa (son gestores operativos).
     * Auxiliar/Empleado necesitan puede_leer o carpeta pública.
     */
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

    /**
     * Editar metadata (descripción).
     * Admin/Gerente pueden gestionar metadata de su empresa.
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
     * Admin puede eliminar cualquier archivo de su empresa.
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
     * DESCARGAR — regla confirmada:
     *
     * TODOS los roles (incluido Admin y Gerente) respetan PermisoCarpeta.
     * La única excepción es carpeta pública (es_publico = 1):
     * si la carpeta es pública, TODOS de la misma empresa pueden descargar.
     *
     * Para descargar de otra empresa: CompanyScope verifica solicitud aprobada,
     * y luego la ArchivoPolicy::download() devuelve false (empresa distinta)
     * a menos que se maneje explícitamente — por ahora solo usuarios misma empresa.
     */
    public function download(Usuario $usuario, Archivo $archivo): bool
    {
        $carpeta = $archivo->carpeta;

        // Bloquear acceso de otra empresa (CompanyScope ya debió validar solicitudes)
        if ($carpeta->empresa_id !== $usuario->empresa_id) {
            return false;
        }

        // Carpeta pública → todos de la empresa pueden descargar
        if ($carpeta->es_publico) {
            return true;
        }

        // Carpeta privada → TODOS los roles necesitan puede_descargar en PermisoCarpeta
        // (incluido Admin y Gerente — decisión confirmada)
        return $carpeta->usuarioPuedeDescargar($usuario);
    }

    public function restore(Usuario $usuario, Archivo $archivo): bool
    {
        return $archivo->carpeta->empresa_id === $usuario->empresa_id
            && $usuario->rol === 'Admin';
    }
}