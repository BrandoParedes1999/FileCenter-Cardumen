<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
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

    // ── Relaciones ────────────────────────────────────

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class);
    }

    public function carpetas(): HasMany
    {
        return $this->hasMany(Carpeta::class);
    }

    public function permisosCarpeta(): HasMany
    {
        return $this->hasMany(PermisoCarpeta::class);
    }

    // ── Helpers ───────────────────────────────────────

    /** URL pública del logo */
    public function getLogoUrlAttribute(): string
    {
        return asset('images/empresas/' . ($this->logo ?? 'logo_default.png'));
    }

    /** Color primario con fallback */
    public function getColorBgAttribute(): string
    {
        return $this->color_primario ?? '#1B3A6B';
    }

    /** Color secundario (acento/badge) con fallback */
    public function getColorAccentAttribute(): string
    {
        return $this->color_secundario ?? '#2E5FA3';
    }

    /**
     * Genera el style inline para un badge de empresa.
     * Uso en Blade: {!! $empresa->badgeStyle() !!}
     */
    public function badgeStyle(): string
    {
        $accent = $this->color_accent;
        return "background:{$accent}18;color:{$accent};border:1px solid {$accent}33;padding:2px 10px;border-radius:999px;font-size:12px;font-weight:500";
    }

    /** Scope: solo empresas activas */
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    /** Scope: corporativo primero */
    public function scopeOrdenadas($query)
    {
        return $query->orderByDesc('es_corporativo')->orderBy('nombre');
    }
}