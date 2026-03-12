<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroActividad extends Model
{
    use HasFactory;

    protected $table = 'registro_de_actividad';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'accion',
        'recurso',
        'recurso_id',
        'detalles',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Valores ENUM válidos para 'accion'
    const ACCIONES = [
        'subir', 'descargar', 'eliminar', 'editar',
        'crear_carpeta', 'mover', 'ver',
        'solicitar_acceso', 'aprobar_solicitud', 'rechazar_solicitud',
        'restaurar_version',
        'iniciar_sesion', 'cerrar_sesion', 'login_fallido', 'usuario_bloqueado',
    ];

    // Valores ENUM válidos para 'recurso'
    const RECURSOS = ['archivo', 'carpeta', 'solicitud', 'usuario', 'version'];

    // RELACIONES

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }


    // METODOS


    /**
     * Registra una acción de auditoría.
     *
     * Uso:
     *   RegistroActividad::registrar('subir', 'archivo', $archivo->id, 'Subió reporte.pdf');
     *   RegistroActividad::registrar('login_fallido', 'usuario', $usuario->id, 'Intento fallido');
     */
    public static function registrar(
        string  $accion,
        string  $recurso,
        int     $recursoId,
        ?string $detalles = null,
        ?int    $usuarioId = null,
        ?string $ip = null
    ): self {
        return static::create([
            'usuario_id' => $usuarioId ?? (auth()->check() ? auth()->id() : null),
            'accion'     => $accion,
            'recurso'    => $recurso,
            'recurso_id' => $recursoId,
            'detalles'   => $detalles,
            'ip_address' => $ip ?? request()->ip(),
            'created_at' => now(),
        ]);
    }

    // SCOPES

    public function scopeDeUsuario($query, int $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    public function scopeDeAccion($query, string $accion)
    {
        return $query->where('accion', $accion);
    }

    public function scopeRecientes($query, int $dias = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }
}