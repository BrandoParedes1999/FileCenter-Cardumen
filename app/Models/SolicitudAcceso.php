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
     * Aprueba la solicitud y otorga los PermisoCarpeta necesarios automáticamente.
     *
     * Tres casos:
     *  1. carpeta_id → permiso directo + acceso de navegación a carpetas ancestro
     *  2. archivo_id (sin carpeta) → permiso en la carpeta padre del archivo + ancestros
     *  3. Sin recurso → acceso a TODAS las carpetas raíz de la empresa (con herencia)
     */
    public function aprobar(int $revisorId, ?string $comentario = null, ?\Carbon\Carbon $caduca = null): void
    {
        // ── Caso 1: carpeta específica ─────────────────────────────────────────
        if ($this->carpeta_id) {
            $carpeta   = $this->carpeta ?? Carpeta::find($this->carpeta_id);
            $empresaId = $carpeta?->empresa_id;

            PermisoCarpeta::updateOrCreate(
                ['carpeta_id' => $this->carpeta_id, 'usuario_id' => $this->solicitante_id],
                [
                    'empresa_id'      => $empresaId,
                    'puede_leer'      => true,
                    'puede_descargar' => $this->tipo_acceso === 'Descargar',
                    'puede_subir'     => false,
                    'puede_editar'    => false,
                    'puede_borrar'    => false,
                    'concedido_por'   => $revisorId,
                    'heredar'         => true,
                ]
            );
            Carpeta::invalidarCachePermisos($this->carpeta_id);

            // Dar acceso de navegación (solo lectura, sin herencia) a las carpetas ancestro
            // para que el empleado pueda llegar desde el índice hasta la carpeta aprobada.
            $this->otorgarNavegacionAncestros($carpeta?->padre_id, $revisorId);
        }

        // ── Caso 2: archivo específico (sin carpeta) ───────────────────────────
        if ($this->archivo_id && !$this->carpeta_id) {
            $archivo   = $this->archivo ?? Archivo::find($this->archivo_id);
            $carpetaId = $archivo?->carpeta_id;

            if ($carpetaId) {
                $carpeta = $archivo->carpeta ?? Carpeta::find($carpetaId);

                PermisoCarpeta::updateOrCreate(
                    ['carpeta_id' => $carpetaId, 'usuario_id' => $this->solicitante_id],
                    [
                        'empresa_id'      => $carpeta?->empresa_id,
                        'puede_leer'      => true,
                        'puede_descargar' => $this->tipo_acceso === 'Descargar',
                        'puede_subir'     => false,
                        'puede_editar'    => false,
                        'puede_borrar'    => false,
                        'concedido_por'   => $revisorId,
                        'heredar'         => false,
                    ]
                );
                Carpeta::invalidarCachePermisos($carpetaId);

                $this->otorgarNavegacionAncestros($carpeta?->padre_id, $revisorId);
            }
        }

        // ── Caso 3: acceso general a la empresa (sin recurso específico) ────────
        if (!$this->carpeta_id && !$this->archivo_id) {
            Carpeta::where('empresa_id', $this->empresa_objetivo_id)
                ->whereNull('padre_id')
                ->each(function (Carpeta $raiz) use ($revisorId) {
                    PermisoCarpeta::updateOrCreate(
                        ['carpeta_id' => $raiz->id, 'usuario_id' => $this->solicitante_id],
                        [
                            'empresa_id'      => $this->empresa_objetivo_id,
                            'puede_leer'      => true,
                            'puede_descargar' => $this->tipo_acceso === 'Descargar',
                            'puede_subir'     => false,
                            'puede_editar'    => false,
                            'puede_borrar'    => false,
                            'concedido_por'   => $revisorId,
                            'heredar'         => true,
                        ]
                    );
                    Carpeta::invalidarCachePermisos($raiz->id);
                });
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
     * Otorga permiso de solo-navegación (puede_leer, sin herencia) a las carpetas
     * ancestro del camino hasta la raíz, para que el empleado pueda llegar allí
     * desde el índice. Usa firstOrCreate para no sobreescribir permisos existentes.
     */
    private function otorgarNavegacionAncestros(?int $padreId, int $revisorId): void
    {
        while ($padreId) {
            $ancestro = Carpeta::find($padreId);
            if (!$ancestro) break;

            PermisoCarpeta::firstOrCreate(
                ['carpeta_id' => $ancestro->id, 'usuario_id' => $this->solicitante_id],
                [
                    'empresa_id'      => $ancestro->empresa_id,
                    'puede_leer'      => true,
                    'puede_descargar' => false,
                    'puede_subir'     => false,
                    'puede_editar'    => false,
                    'puede_borrar'    => false,
                    'concedido_por'   => $revisorId,
                    'heredar'         => false,
                ]
            );
            Carpeta::invalidarCachePermisos($ancestro->id);
            $padreId = $ancestro->padre_id;
        }
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