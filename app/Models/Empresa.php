<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $table = 'empresas';

    protected $fillable = [
        'nombre',
        'siglas',
        'logo',
        'es_corporativo',
        'color_primario',
        'color_secundario',
        'color_terciario',
        'activo',
    ];

    protected $casts = [
        'es_corporativo' => 'boolean',
        'activo'         => 'boolean',
    ];

    // RELACIONES

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'empresa_id');
    }

    public function carpetas()
    {
        return $this->hasMany(Carpeta::class, 'empresa_id');
    }

    public function carpetasRaiz()
    {
        return $this->hasMany(Carpeta::class, 'empresa_id')
                    ->whereNull('padre_id');
    }

    // SCOPES

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopeCorporativo($query)
    {
        return $query->where('es_corporativo', true);
    }
}