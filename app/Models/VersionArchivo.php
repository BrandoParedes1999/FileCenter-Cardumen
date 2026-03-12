<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VersionArchivo extends Model
{
    use HasFactory;

    protected $table = 'versiones_archivos';

    // Solo tiene created_at, sin updated_at
    public $timestamps = false;

    protected $fillable = [
        'archivo_id',
        'version',
        'nombre_original',
        'nombre_almacenamiento',
        'ruta_disco',
        'hash_sha256',
        'tamanio_bytes',
        'subido_por',
        'nota_version',
        'activo',
    ];

    protected $casts = [
        'version'       => 'integer',
        'tamanio_bytes' => 'integer',
        'activo'        => 'boolean',
        'created_at'    => 'datetime',
    ];

    protected $attributes = [
        'activo' => true,
    ];

    // RELACIONES

    public function archivo()
    {
        return $this->belongsTo(Archivo::class, 'archivo_id');
    }

    public function subidoPor()
    {
        return $this->belongsTo(Usuario::class, 'subido_por');
    }

    // HELPERS

    public function tamanioFormateado(): string
    {
        $bytes = $this->tamanio_bytes;

        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return number_format($bytes / 1024, 2) . ' KB';

        return $bytes . ' B';
    }

    // BOOT — forzar created_at al crear

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->created_at = now();
        });
    }
}