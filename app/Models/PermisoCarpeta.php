<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermisoCarpeta extends Model
{
    use HasFactory;

    protected $table = 'permisos_de_carpeta';

    protected $fillable = [
        'carpeta_id',
        'usuario_id',
        'empresa_id',
        'rol',
        'puede_leer',
        'puede_subir',
        'puede_editar',
        'puede_borrar',
        'puede_descargar',
        'heredar',
        'concedido_por',
    ];

    protected $casts = [
        'puede_leer'      => 'boolean',
        'puede_subir'     => 'boolean',
        'puede_editar'    => 'boolean',
        'puede_borrar'    => 'boolean',
        'puede_descargar' => 'boolean',
        'heredar'         => 'boolean',
    ];

    protected $attributes = [
        'heredar' => true,
    ];

    // RELACIONES 

    public function carpeta()
    {
        return $this->belongsTo(Carpeta::class, 'carpeta_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function concedidoPor()
    {
        return $this->belongsTo(Usuario::class, 'concedido_por');
    }

    // METODOS

    /**
     * Crea permiso de solo lectura para un usuario.
     */
    public static function soloLectura(int $carpetaId, int $usuarioId, int $concedidoPor): self
    {
        return static::create([
            'carpeta_id'      => $carpetaId,
            'usuario_id'      => $usuarioId,
            'puede_leer'      => true,
            'puede_subir'     => false,
            'puede_editar'    => false,
            'puede_borrar'    => false,
            'puede_descargar' => true,
            'concedido_por'   => $concedidoPor,
        ]);
    }

    /**
     * Crea permiso completo (admin de carpeta) para un usuario.
     */
    public static function accesoCompleto(int $carpetaId, int $usuarioId, int $concedidoPor): self
    {
        return static::create([
            'carpeta_id'      => $carpetaId,
            'usuario_id'      => $usuarioId,
            'puede_leer'      => true,
            'puede_subir'     => true,
            'puede_editar'    => true,
            'puede_borrar'    => true,
            'puede_descargar' => true,
            'concedido_por'   => $concedidoPor,
        ]);
    }

    // SCOPES

    public function scopeDeUsuario($query, int $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    public function scopeDeCarpeta($query, int $carpetaId)
    {
        return $query->where('carpeta_id', $carpetaId);
    }

    public function scopeDeRol($query, string $rol)
    {
        return $query->where('rol', $rol);
    }
}