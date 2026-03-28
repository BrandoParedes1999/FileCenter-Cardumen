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
        'modo_acceso',                  // 'solo_lectura' | 'con_descarga' | 'normal'
        'requiere_aprobacion_subida',   // bool: Auxiliar/Empleado deben pedir aprobación
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

    /**
     * ¿La carpeta es de solo lectura? (sin descarga para nadie salvo permiso explícito)
     */
    public function esSoloLectura(): bool
    {
        return $this->modo_acceso === 'solo_lectura';
    }

    /**
     * ¿La carpeta permite descarga por defecto?
     */
    public function permiteDescargaBase(): bool
    {
        return in_array($this->modo_acceso, ['con_descarga', 'normal']);
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

        if ($permiso) {
            return $permiso;
        }

        // 2. Permiso por empresa + rol
        $permiso = $this->permisos()
            ->where('empresa_id', $usuario->empresa_id)
            ->where('rol', $usuario->rol)
            ->first();

        if ($permiso) {
            return $permiso;
        }

        // 3. Heredar del padre
        if ($this->padre_id) {
            $padrePermiso = $this->padre->permisoEfectivo($usuario);
            // Solo hereda si el permiso del padre tiene heredar=1
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
     * ¿El usuario puede descargar?
     *
     * Regla combinada:
     * 1. Superadmin / Aux_QHSE → siempre sí
     * 2. Carpeta en modo 'solo_lectura' → nadie descarga salvo que tenga
     *    puede_descargar explícito en su PermisoCarpeta
     * 3. Resto: verifica puede_descargar en el permiso efectivo
     */
    public function usuarioPuedeDescargar(Usuario $usuario): bool
    {
        if ($usuario->esSuperAdmin() || $usuario->esAuxQHSE()) return true;

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