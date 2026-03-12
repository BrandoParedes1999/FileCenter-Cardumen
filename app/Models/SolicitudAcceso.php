<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudAcceso extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_acceso';

    protected $fillable = [
        'solicitante_id',
        'empresa_objetivo_id',
        'carpeta_id',
        'archivo_id',
        'razon',
        'status',
        'revisado_por',
        'revisado_en',
        'comentario_revisor',
        'tipo_acceso',
        'caduca_en',
    ];

    protected $casts = [
        'revisado_en' => 'datetime',
        'caduca_en'   => 'datetime',
    ];

    // RELACIONES

    public function solicitante()
    {
        return $this->belongsTo(Usuario::class, 'solicitante_id');
    }

    public function empresaObjetivo()
    {
        return $this->belongsTo(Empresa::class, 'empresa_objetivo_id');
    }

    public function carpeta()
    {
        return $this->belongsTo(Carpeta::class, 'carpeta_id');
    }

    public function archivo()
    {
        return $this->belongsTo(Archivo::class, 'archivo_id');
    }

    public function revisor()
    {
        return $this->belongsTo(Usuario::class, 'revisado_por');
    }

    // HELPERS DE ESTADO

    public function estaPendiente(): bool
    {
        return $this->status === 'Pendiente';
    }

    public function fueAprobada(): bool
    {
        return $this->status === 'Aprobado';
    }

    public function fueRechazada(): bool
    {
        return $this->status === 'Rechazado';
    }

    public function estaVigente(): bool
    {
        return $this->fueAprobada()
            && ($this->caduca_en === null || $this->caduca_en->isFuture());
    }

    /**
     * Aprueba la solicitud y crea el PermisoCarpeta correspondiente.
     */
    public function aprobar(int $revisorId, ?string $comentario = null, ?\Carbon\Carbon $caduca = null): void
    {
        // Crear permiso en la carpeta si aplica
        if ($this->carpeta_id) {
            $datos = [
                'puede_leer'      => true,
                'puede_descargar' => $this->tipo_acceso === 'Descargar',
                'puede_subir'     => false,
                'puede_editar'    => false,
                'puede_borrar'    => false,
                'concedido_por'   => $revisorId,
            ];

            PermisoCarpeta::updateOrCreate(
                ['carpeta_id' => $this->carpeta_id, 'usuario_id' => $this->solicitante_id],
                $datos
            );
        }

        $this->update([
            'status'             => 'Aprobado',
            'revisado_por'       => $revisorId,
            'revisado_en'        => now(),
            'comentario_revisor' => $comentario,
            'caduca_en'          => $caduca,
        ]);
    }

    /**
     * Rechaza la solicitud.
     */
    public function rechazar(int $revisorId, ?string $comentario = null): void
    {
        $this->update([
            'status'             => 'Rechazado',
            'revisado_por'       => $revisorId,
            'revisado_en'        => now(),
            'comentario_revisor' => $comentario,
        ]);
    }

    // SCOPES

    public function scopePendientes($query)
    {
        return $query->where('status', 'Pendiente');
    }

    public function scopeDeEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_objetivo_id', $empresaId);
    }
}