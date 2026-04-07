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

    public function view(Usuario $usuario, Archivo $archivo): bool
    {
        $carpeta = $archivo->carpeta;

        // Corporativo → todos los usuarios activos pueden ver
        if ($this->esCarpetaCorporativa($carpeta)) return true;

        if ($carpeta->empresa_id !== $usuario->empresa_id) {
            // Cross-empresa solo con solicitud aprobada
            return \App\Models\SolicitudAcceso::where('solicitante_id', $usuario->id)
                ->where(function ($q) use ($archivo, $carpeta) {
                    $q->where('archivo_id', $archivo->id)
                      ->orWhere('carpeta_id', $carpeta->id);
                })
                ->where('status', 'Aprobado')
                ->where(function ($q) {
                    $q->whereNull('caduca_en')->orWhere('caduca_en', '>', now());
                })
                ->exists();
        }

        if ($carpeta->es_publico) return true;
        if (in_array($usuario->rol, ['Admin', 'Gerente'])) return true;

        return $carpeta->usuarioPuedeLeer($usuario);
    }

    public function create(Usuario $usuario): bool
    {
        // Empleado también puede intentar subir; si la carpeta tiene
        // requiere_aprobacion_subida=true, ArchivoController lo enruta
        // a la cola de solicitudes pendientes en lugar de publicar directo.
        return in_array($usuario->rol, ['Admin', 'Gerente', 'Auxiliar', 'Empleado']);
    }

    /**
     * Editar metadata (descripción).
     */
    public function update(Usuario $usuario, Archivo $archivo): bool
    {
        $carpeta = $archivo->carpeta;

        // Corporativo: solo Admin/Gerente/Superadmin pueden editar
        if ($this->esCarpetaCorporativa($carpeta)) {
            return in_array($usuario->rol, ['Admin', 'Gerente']);
        }

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

        // Nadie externo puede borrar del corporativo (solo SA/AuxQHSE via before())
        if ($this->esCarpetaCorporativa($carpeta)) {
            return false;
        }

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
     * ► Si existe un permiso EXPLÍCITO (por usuario) para este usuario,
     *   ese permiso tiene prioridad absoluta, incluso sobre carpetas públicas.
     *   Esto arregla el bug donde remover puede_descargar no tenía efecto.
     *
     * ► Si NO hay permiso explícito, se aplican las reglas por defecto
     *   (corporativo libre, carpeta pública libre, etc.).
     */
    public function download(Usuario $usuario, Archivo $archivo): bool
    {
        $carpeta = $archivo->carpeta;

        // ── Corporativo ───────────────────────────────────────────────────
        if ($this->esCarpetaCorporativa($carpeta)) {
            if ($carpeta->esSoloLectura()) {
                $p = $carpeta->permisoEfectivo($usuario);
                return $p && $p->puede_descargar;
            }
            // Modo normal/con_descarga: comprobar si hay permiso explícito
            $p = $carpeta->permisoEfectivo($usuario);
            if ($p !== null) {
                return $p->puede_descargar;
            }
            // Sin permiso explícito en corporativo → acceso libre
            return true;
        }

        // ── Cross-empresa (no corporativo) ────────────────────────────────
        if ($carpeta->empresa_id !== $usuario->empresa_id) {
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

        // ── Carpeta en modo solo_lectura (misma empresa) ──────────────────
        if ($carpeta->esSoloLectura()) {
            $p = $carpeta->permisoEfectivo($usuario);
            return $p && $p->puede_descargar;
        }

        // ── Verificar permiso explícito PRIMERO (prioridad sobre es_publico) ─
        // Esto soluciona el bug: si quitas puede_descargar a un usuario en una
        // carpeta pública, el cambio ahora SÍ tiene efecto.
        $permisoExplicito = $carpeta->permisos()
            ->where('usuario_id', $usuario->id)
            ->first();

        if ($permisoExplicito !== null) {
            return (bool) $permisoExplicito->puede_descargar;
        }

        // ── Sin permiso individual: verificar permiso por rol ─────────────
        $permisoRol = $carpeta->permisoEfectivo($usuario); // busca empresa+rol
        if ($permisoRol !== null) {
            return (bool) $permisoRol->puede_descargar;
        }

        // ── Sin ningún permiso explícito: aplicar regla por defecto ──────
        // Carpeta pública de la misma empresa → puede descargar
        if ($carpeta->es_publico) {
            return true;
        }

        // Carpeta privada sin permiso → no puede descargar
        return false;
    }

    public function restore(Usuario $usuario, Archivo $archivo): bool
    {
        return $archivo->carpeta->empresa_id === $usuario->empresa_id
            && $usuario->rol === 'Admin';
    }

    // ── Helpers ─────────────────────────────────────────────────

    private function esCarpetaCorporativa(\App\Models\Carpeta $carpeta): bool
    {
        if ($carpeta->relationLoaded('empresa')) {
            return (bool) $carpeta->empresa?->es_corporativo;
        }

        return \App\Models\Empresa::where('id', $carpeta->empresa_id)
            ->where('es_corporativo', true)
            ->exists();
    }
}