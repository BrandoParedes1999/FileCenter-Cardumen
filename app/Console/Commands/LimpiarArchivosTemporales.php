<?php

namespace App\Console\Commands;

use App\Models\SolicitudSubida;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class LimpiarArchivosTemporales extends Command
{
    protected $signature   = 'filecenter:limpiar-temporales
                                {--dias=7 : Eliminar archivos de solicitudes rechazadas/aprobadas con más de N días}
                                {--dry-run : Mostrar qué se eliminaría sin borrar nada}';

    protected $description = 'Elimina archivos temporales huérfanos de solicitudes de subida procesadas o expiradas';

    public function handle(): int
    {
        $dias   = (int) $this->option('dias');
        $dryRun = (bool) $this->option('dry-run');
        $limite = now()->subDays($dias);

        // 1. Solicitudes procesadas (aprobadas o rechazadas) cuyo archivo temp aún existe
        $procesadas = SolicitudSubida::whereIn('status', ['Aprobado', 'Rechazado'])
            ->where('updated_at', '<', $limite)
            ->whereNotNull('ruta_temporal')
            ->get();

        // 2. Solicitudes pendientes muy antiguas (sin actividad por 30 días)
        $abandonadas = SolicitudSubida::where('status', 'Pendiente')
            ->where('created_at', '<', now()->subDays(30))
            ->whereNotNull('ruta_temporal')
            ->get();

        $candidatos = $procesadas->merge($abandonadas);

        if ($candidatos->isEmpty()) {
            $this->info('No hay archivos temporales que limpiar.');
            return self::SUCCESS;
        }

        $this->info("Encontrados {$candidatos->count()} registros con archivos temporales.");

        $eliminados   = 0;
        $noEncontrados = 0;

        foreach ($candidatos as $solicitud) {
            $ruta = $solicitud->ruta_temporal;

            if ($dryRun) {
                $existe = Storage::disk('filecenter')->exists($ruta);
                $estado = $existe ? '[EXISTE]' : '[YA ELIMINADO]';
                $this->line("  {$estado} {$ruta}  (id={$solicitud->id}, status={$solicitud->status})");
                continue;
            }

            if (Storage::disk('filecenter')->exists($ruta)) {
                Storage::disk('filecenter')->delete($ruta);
                $eliminados++;
            } else {
                $noEncontrados++;
            }

            // Limpiar la referencia en BD para que no se vuelva a procesar
            $solicitud->updateQuietly(['ruta_temporal' => null]);
        }

        if ($dryRun) {
            $this->warn('Modo --dry-run: no se eliminó nada.');
            return self::SUCCESS;
        }

        $this->info("Archivos eliminados: {$eliminados}");

        if ($noEncontrados > 0) {
            $this->comment("Registros sin archivo en disco (ya limpiados): {$noEncontrados}");
        }

        return self::SUCCESS;
    }
}
