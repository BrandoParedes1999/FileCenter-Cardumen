<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Archivo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'archivos';

    protected $fillable = [
        'carpeta_id',
        'subido_por',
        'nombre_original',
        'nombre_almacenamiento',
        'ruta_disco',
        'hash_sha256',
        'tipo_mime',
        'extension',
        'tamanio_bytes',
        'descripcion',
        'version',
        'numero_descargas',
        'esta_eliminado',
    ];

    protected $casts = [
        'tamanio_bytes'    => 'integer',
        'version'          => 'integer',
        'numero_descargas' => 'integer',
        'esta_eliminado'   => 'boolean',
        'deleted_at'       => 'datetime',
    ];

    // RELACIONES

    public function carpeta()
    {
        return $this->belongsTo(Carpeta::class, 'carpeta_id');
    }

    public function subidoPor()
    {
        return $this->belongsTo(Usuario::class, 'subido_por');
    }

    public function versiones()
    {
        return $this->hasMany(VersionArchivo::class, 'archivo_id')
                    ->orderBy('version', 'desc');
    }

    public function versionActual()
    {
        return $this->hasOne(VersionArchivo::class, 'archivo_id')
                    ->where('version', $this->version)
                    ->where('activo', true);
    }

    public function solicitudes()
    {
        return $this->hasMany(SolicitudAcceso::class, 'archivo_id');
    }

    // HELPERS

    public function incrementarDescargas(): void
    {
        $this->increment('numero_descargas');
    }

    public function tamanioFormateado(): string
    {
        $bytes = $this->tamanio_bytes;
        if ($bytes < 1024)       return "{$bytes} B";
        if ($bytes < 1048576)    return round($bytes / 1024, 1) . ' KB';
        if ($bytes < 1073741824) return round($bytes / 1048576, 1) . ' MB';
        return round($bytes / 1073741824, 2) . ' GB';
    }

    /** Icono Bootstrap según tipo MIME */
    public function iconoTipo(): string
    {
        return match (true) {
            $this->extension === 'pdf'                          => 'bi-file-earmark-pdf text-danger',
            in_array($this->extension, ['doc', 'docx'])        => 'bi-file-earmark-word text-primary',
            in_array($this->extension, ['xls', 'xlsx'])        => 'bi-file-earmark-excel text-success',
            in_array($this->extension, ['ppt', 'pptx'])        => 'bi-file-earmark-ppt text-warning',
            in_array($this->extension, ['jpg', 'jpeg', 'png',
                                        'gif', 'webp'])       => 'bi-file-earmark-image text-info',
            in_array($this->extension, ['zip', 'rar', '7z'])   => 'bi-file-earmark-zip text-warning',
            default                                             => 'bi-file-earmark text-secondary',
        };
    }

    // SCOPES

    public function scopeDeCarpeta($query, int $carpetaId)
    {
        return $query->where('carpeta_id', $carpetaId);
    }

    public function scopeActivos($query)
    {
        return $query->where('esta_eliminado', false);
    }
}
