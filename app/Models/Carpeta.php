<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Carpeta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'carpetas';

    protected $fillable = [
        'empresa_id',
        'padre_id',
        'nombre',
        'path',
        'es_publico',
        'modo_acceso',
        'requiere_aprobacion_subida',
        'creado_por',
    ];

    protected $casts = [
        'es_publico'                 => 'boolean',
        'requiere_aprobacion_subida' => 'boolean',
        'deleted_at'                 => 'datetime',
    ];

    protected $attributes = [
        'modo_acceso'                => 'normal',
        'requiere_aprobacion_subida' => false,
    ];

    // RELACIONES

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function padre()
    {
        return $this->belongsTo(Carpeta::class, 'padre_id');
    }

    public function hijos()
    {
        return $this->hasMany(Carpeta::class, 'padre_id');
    }

    public function descendientes()
    {
        return $this->hijos()->with('descendientes');
    }

    public function creadoPor()
    {
        return $this->belongsTo(Usuario::class, 'creado_por');
    }

    public function archivos()
    {
        return $this->hasMany(Archivo::class, 'carpeta_id');
    }

    public function permisos()
    {
        return $this->hasMany(PermisoCarpeta::class, 'carpeta_id');
    }

    public function solicitudes()
    {
        return $this->hasMany(SolicitudAcceso::class, 'carpeta_id');
    }

    public function solicitudesSubida()
    {
        return $this->hasMany(SolicitudSubida::class, 'carpeta_id');
    }

    // HELPERS DE MODO DE ACCESO

    public function esSoloLectura(): bool
    {
        return $this->modo_acceso === 'solo_lectura';
    }

    public function permiteDescargaBase(): bool
    {
        return in_array($this->modo_acceso, ['con_descarga', 'normal']);
    }

    /**
     * Determina si esta carpeta pertenece a la empresa corporativa.
     * Usa la relación si ya está cargada para evitar queries extra.
     */
    public function esDeCorporativo(): bool
    {
        if ($this->relationLoaded('empresa')) {
            return (bool) $this->empresa?->es_corporativo;
        }

        return Empresa::where('id', $this->empresa_id)
            ->where('es_corporativo', true)
            ->exists();
    }

    // LÓGICA DE PERMISOS CON HERENCIA

    /**
     * Obtiene el permiso efectivo de un usuario en esta carpeta.
     * Primero busca permiso directo por usuario_id,
     * luego por empresa+rol, y si heredar=1 sube al padre.
     */
    public function permisoEfectivo(Usuario $usuario): ?PermisoCarpeta
    {
        // 1. Permiso directo individual
        $permiso = $this->permisos()
            ->where('usuario_id', $usuario->id)
            ->first();
        if ($permiso) return $permiso;

        // 2. Permiso por empresa + rol del usuario
        $permiso = $this->permisos()
            ->where('empresa_id', $usuario->empresa_id)
            ->where('rol', $usuario->rol)
            ->whereNull('usuario_id')
            ->first();
        if ($permiso) return $permiso;

        // 3. Permiso global (empresa_id = null) por rol — para corporativo
        $permiso = $this->permisos()
            ->whereNull('empresa_id')
            ->whereNull('usuario_id')
            ->where('rol', $usuario->rol)
            ->first();
        if ($permiso) return $permiso;

        // 4. Heredar del padre
        if ($this->padre_id) {
            $padrePermiso = $this->padre->permisoEfectivo($usuario);
            if ($padrePermiso && $padrePermiso->heredar) {
                return $padrePermiso;
            }
        }

        return null;
    }

    public function usuarioPuedeLeer(Usuario $usuario): bool
    {
        if ($usuario->esSuperAdmin() || $usuario->esAuxQHSE()) return true;
        if ($this->es_publico) return true;

        // Carpeta del corporativo → todos los usuarios activos pueden leer
        if ($this->esDeCorporativo()) return true;

        if (in_array($usuario->rol, ['Admin', 'Gerente'])
            && $this->empresa_id === $usuario->empresa_id) {
            return true;
        }

        $p = $this->permisoEfectivo($usuario);
        return $p && $p->puede_leer;
    }

    public function usuarioPuedeSubir(Usuario $usuario): bool
    {
        if ($usuario->esSuperAdmin()) return true;

        $p = $this->permisoEfectivo($usuario);
        return $p && $p->puede_subir;
    }

    public function usuarioPuedeEditar(Usuario $usuario): bool
    {
        if ($usuario->esSuperAdmin()) return true;

        $p = $this->permisoEfectivo($usuario);
        return $p && $p->puede_editar;
    }

    public function usuarioPuedeBorrar(Usuario $usuario): bool
    {
        if ($usuario->esSuperAdmin()) return true;

        $p = $this->permisoEfectivo($usuario);
        return $p && $p->puede_borrar;
    }

    /**
     * ¿El usuario puede descargar archivos de esta carpeta?
     *
     * Reglas por prioridad:
     * 1. Superadmin / Aux_QHSE → siempre sí.
     * 2. Carpeta del corporativo → todos los usuarios activos pueden descargar,
     *    SALVO modo 'solo_lectura' donde se exige permiso explícito.
     * 3. Carpeta en modo 'solo_lectura' (misma empresa) → necesita
     *    puede_descargar explícito en PermisoCarpeta.
     * 4. Resto → verifica puede_descargar en el permiso efectivo.
     */
    public function usuarioPuedeDescargar(Usuario $usuario): bool
    {
        if ($usuario->esSuperAdmin() || $usuario->esAuxQHSE()) return true;

        // Corporativo: todos pueden descargar salvo modo solo_lectura
        if ($this->esDeCorporativo()) {
            if ($this->esSoloLectura()) {
                $p = $this->permisoEfectivo($usuario);
                return $p && $p->puede_descargar;
            }
            return true;
        }

        $p = $this->permisoEfectivo($usuario);

        // En modo solo_lectura se necesita permiso explícito de descarga
        if ($this->esSoloLectura()) {
            return $p && $p->puede_descargar;
        }

        return $p && $p->puede_descargar;
    }

    // SCOPES

    public function scopeDeEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeRaices($query)
    {
        return $query->whereNull('padre_id');
    }

    public function scopePublicas($query)
    {
        return $query->where('es_publico', true);
    }
}