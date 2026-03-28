<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Archivo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'archivos';

    // Extensiones permitidas en el sistema
    const EXTENSIONES_PERMITIDAS = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];

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

    /**
     * Color del icono según extensión (solo tipos permitidos).
     * Retorna ['color' => hex, 'bg' => rgba]
     */
    public function colorExtension(): array
    {
        return match (strtolower($this->extension)) {
            'pdf'        => ['color' => '#dc2626', 'bg' => 'rgba(220,38,38,0.1)'],
            'doc', 'docx'=> ['color' => '#2563eb', 'bg' => 'rgba(37,99,235,0.1)'],
            'xls', 'xlsx'=> ['color' => '#059669', 'bg' => 'rgba(5,150,105,0.1)'],
            default      => ['color' => '#64748b', 'bg' => 'rgba(100,116,139,0.1)'],
        };
    }

    /**
     * Clase Bootstrap Icon según extensión.
     * Solo contempla los tipos permitidos en el sistema.
     */
    public function iconoTipo(): string
    {
        return match (strtolower($this->extension)) {
            'pdf'        => 'bi-file-earmark-pdf text-danger',
            'doc', 'docx'=> 'bi-file-earmark-word text-primary',
            'xls', 'xlsx'=> 'bi-file-earmark-excel text-success',
            default      => 'bi-file-earmark text-secondary',
        };
    }

    /**
     * Etiqueta legible del tipo de archivo.
     */
    public function tipoLegible(): string
    {
        return match (strtolower($this->extension)) {
            'pdf'  => 'Documento PDF',
            'doc'  => 'Word 97-2003',
            'docx' => 'Word',
            'xls'  => 'Excel 97-2003',
            'xlsx' => 'Excel',
            default => strtoupper($this->extension),
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