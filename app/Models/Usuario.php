<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $table = 'usuarios';

    protected $fillable = [
        'empresa_id',
        'nombre',
        'paterno',
        'materno',
        'email',
        'password',
        'rol',
        'departamento',
        'avatar',
        'es_activo',
        'intentos_login',
        'bloqueado_hasta',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'es_activo'         => 'boolean',
        'bloqueado_hasta'   => 'datetime',
        'last_login'        => 'datetime',
        'deleted_at'        => 'datetime',
    ];

    // RELACIONES

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function carpetas()
    {
        return $this->hasMany(Carpeta::class, 'creado_por');
    }

    public function archivos()
    {
        return $this->hasMany(Archivo::class, 'subido_por');
    }

    public function permisosCarpeta()
    {
        return $this->hasMany(PermisoCarpeta::class, 'usuario_id');
    }

    public function solicitudesEnviadas()
    {
        return $this->hasMany(SolicitudAcceso::class, 'solicitante_id');
    }

    public function solicitudesRevisadas()
    {
        return $this->hasMany(SolicitudAcceso::class, 'revisado_por');
    }

    public function actividad()
    {
        return $this->hasMany(RegistroActividad::class, 'usuario_id');
    }

    // NOMBRE COMPLETO

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombre} {$this->paterno} {$this->materno}");
    }

    // HELPERS DE ROL

    /**
     * Asigna rol en Spatie Y actualiza el ENUM local (caché rápido).
     */
    public function assignRoleSynced(string $roleName): void
    {
        $this->syncRoles([$roleName]);
        $this->update(['rol' => $roleName]);
    }

    public function esSuperAdmin(): bool
    {
        return $this->rol === 'Superadmin';
    }

    public function esAuxQHSE(): bool
    {
        return $this->rol === 'Aux_QHSE';
    }

    public function esAdmin(): bool
    {
        return $this->rol === 'Admin';
    }

    public function esGerente(): bool
    {
        return $this->rol === 'Gerente';
    }

    // PROTECCION CONTRA FUERZA BRUTA

    public function estaBloqueado(): bool
    {
        return $this->bloqueado_hasta && $this->bloqueado_hasta->isFuture();
    }

    public function incrementarIntentos(): void
    {
        $this->increment('intentos_login');

        if ($this->fresh()->intentos_login >= 5) {
            $this->update([
                'bloqueado_hasta' => now()->addMinutes(15),
                'intentos_login'  => 0,
            ]);
        }
    }

    public function resetearIntentos(): void
    {
        $this->update([
            'intentos_login'  => 0,
            'bloqueado_hasta' => null,
            'last_login'      => now(),
        ]);
    }

    // SCOPES

    public function scopeDeEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeActivos($query)
    {
        return $query->where('es_activo', true);
    }
}
