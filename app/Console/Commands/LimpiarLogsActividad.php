<?php

namespace App\Console\Commands;

use App\Models\RegistroActividad;
use Illuminate\Console\Command;

class LimpiarLogsActividad extends Command
{
    protected $signature   = 'filecenter:limpiar-logs {--dias=90 : Eliminar registros más antiguos que N días}';
    protected $description = 'Elimina registros de actividad más antiguos que el número de días indicado';

    public function handle(): int
    {
        $dias  = (int) $this->option('dias');
        $corte = now()->subDays($dias);

        $this->info("Eliminando registros de actividad anteriores a {$corte->format('d/m/Y')} ({$dias} días)…");

        $eliminados = RegistroActividad::where('created_at', '<', $corte)->delete();

        $this->info("Se eliminaron {$eliminados} registros.");

        return self::SUCCESS;
    }
}
