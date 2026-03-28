<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudSubida extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_subida';

    protected $fillable = [
        'carpeta_id',
        'solicitante_id',
        'nombre_original',
        'nombre_almacenamiento',
        'ruta_temporal',
        'hash_sha256',
        'tipo_mime',
        'extension',
        'tamanio_bytes',
        'descripcion',
        'status',
        'revisado_por',
        'revisado_en',
        'comentario_revisor',
        'archivo_id',
    ];

    protected $casts = [
        'revisado_en'   => 'datetime',
        'tamanio_bytes' => 'integer',
    ];

    // RELACIONES

    public function carpeta()
    {
        return $this->belongsTo(Carpeta::class, 'carpeta_id');
    }

    public function solicitante()
    {
        return $this->belongsTo(Usuario::class, 'solicitante_id');
    }

    public function revisor()
    {
        return $this->belongsTo(Usuario::class, 'revisado_por');
    }

    public function archivo()
    {
        return $this->belongsTo(Archivo::class, 'archivo_id');
    }

    // HELPERS

    public function estaPendiente(): bool
    {
        return $this->status === 'Pendiente';
    }

    public function fueAprobada(): bool
    {
        return $this->status === 'Aprobado';
    }

    public function tamanioFormateado(): string
    {
        $bytes = $this->tamanio_bytes;
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576)    return round($bytes / 1048576, 1)   . ' MB';
        if ($bytes >= 1024)       return round($bytes / 1024, 1)      . ' KB';
        return $bytes . ' B';
    }

    // SCOPES

    public function scopePendientes($query)
    {
        return $query->where('status', 'Pendiente');
    }

    public function scopeDeCarpeta($query, int $carpetaId)
    {
        return $query->where('carpeta_id', $carpetaId);
    }
}