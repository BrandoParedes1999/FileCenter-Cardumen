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
        return in_array($usuario->rol, ['Admin', 'Gerente', 'Auxiliar']);
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
     * DESCARGAR — lógica completa con soporte de corporativo:
     *
     * 1. Carpeta del corporativo → TODOS los usuarios activos pueden descargar,
     *    SALVO que la carpeta esté en modo 'solo_lectura' (entonces necesitan
     *    permiso explícito de descarga en PermisoCarpeta).
     *
     * 2. Carpeta de otra empresa (cross-empresa, no corporativo): solo si hay
     *    solicitud aprobada con tipo_acceso = 'Descargar'.
     *
     * 3. Carpeta pública de misma empresa: todos pueden descargar
     *    SALVO modo solo_lectura.
     *
     * 4. Resto: verifica puede_descargar en PermisoCarpeta.
     */
    public function download(Usuario $usuario, Archivo $archivo): bool
    {
        $carpeta = $archivo->carpeta;

        // ── Corporativo ───────────────────────────────────────────
        // Todos los usuarios de cualquier empresa pueden descargar
        // del corporativo, excepto si la carpeta es solo_lectura
        // (en ese caso necesitan permiso explícito).
        if ($this->esCarpetaCorporativa($carpeta)) {
            if ($carpeta->esSoloLectura()) {
                $p = $carpeta->permisoEfectivo($usuario);
                return $p && $p->puede_descargar;
            }
            // modo 'con_descarga' o 'normal' → acceso libre
            return true;
        }

        // ── Cross-empresa (no corporativo) ────────────────────────
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

        // ── Carpeta en modo solo_lectura (misma empresa) ──────────
        if ($carpeta->esSoloLectura()) {
            $p = $carpeta->permisoEfectivo($usuario);
            return $p && $p->puede_descargar;
        }

        // ── Carpeta pública de misma empresa ──────────────────────
        if ($carpeta->es_publico) {
            return true;
        }

        // ── Carpeta privada normal ────────────────────────────────
        return $carpeta->usuarioPuedeDescargar($usuario);
    }

    public function restore(Usuario $usuario, Archivo $archivo): bool
    {
        return $archivo->carpeta->empresa_id === $usuario->empresa_id
            && $usuario->rol === 'Admin';
    }

    // ── Helpers ─────────────────────────────────────────────────

    /**
     * Determina si la carpeta pertenece a la empresa corporativa.
     * Usa la relación si ya está cargada; si no, hace una consulta puntual.
     */
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