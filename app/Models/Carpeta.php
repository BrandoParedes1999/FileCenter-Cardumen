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
        'creado_por',
    ];

    protected $casts = [
        'es_publico' => 'boolean',
        'deleted_at' => 'datetime',
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

    public function usuarioPuedeDescargar(Usuario $usuario): bool
    {
        if ($usuario->esSuperAdmin() || $usuario->esAuxQHSE()) return true;

        $p = $this->permisoEfectivo($usuario);
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