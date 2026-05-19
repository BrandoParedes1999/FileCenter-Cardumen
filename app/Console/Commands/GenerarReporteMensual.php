<?php

namespace App\Console\Commands;

use App\Models\RegistroActividad;
use App\Models\Usuario;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class GenerarReporteMensual extends Command
{
    protected $signature   = 'filecenter:reporte-mensual {--mes= : Mes a reportar (YYYY-MM, default: mes anterior}';
    protected $description = 'Genera el reporte CSV de actividad del mes anterior y lo guarda en storage/app/reportes/';

    public function handle(): int
    {
        $mes = $this->option('mes')
            ? Carbon::createFromFormat('Y-m', $this->option('mes'))->startOfMonth()
            : now()->subMonth()->startOfMonth();

        $inicio = $mes->copy()->startOfMonth();
        $fin    = $mes->copy()->endOfMonth();
        $etiq   = $mes->format('Y-m');

        $this->info("Generando reporte de actividad para {$etiq}…");

        $registros = RegistroActividad::with('usuario')
            ->whereBetween('created_at', [$inicio, $fin])
            ->orderBy('created_at')
            ->get();

        if ($registros->isEmpty()) {
            $this->warn("No hay registros para {$etiq}.");
            return self::SUCCESS;
        }

        // Construir CSV
        $bom  = "\xEF\xBB\xBF";
        $rows = [];
        $rows[] = implode(',', ['ID', 'Fecha', 'Usuario', 'Email', 'Accion', 'Recurso', 'Recurso ID', 'Descripcion', 'IP']);

        foreach ($registros as $r) {
            $rows[] = implode(',', [
                $r->id,
                $r->created_at->format('d/m/Y H:i:s'),
                '"' . ($r->usuario ? addslashes($r->usuario->nombre_completo) : 'Sistema') . '"',
                '"' . ($r->usuario?->email ?? '') . '"',
                $r->accion,
                $r->recurso_tipo ?? '',
                $r->recurso_id   ?? '',
                '"' . addslashes($r->descripcion ?? '') . '"',
                $r->ip ?? '',
            ]);
        }

        $contenido = $bom . implode("\n", $rows);
        $path      = "reportes/actividad_{$etiq}.csv";

        Storage::disk('local')->put($path, $contenido);

        $this->info("Reporte guardado en storage/app/{$path} ({$registros->count()} registros).");

        // Notificar a Superadmins por email
        $superadmins = Usuario::where('rol', 'Superadmin')->where('es_activo', true)->get();
        foreach ($superadmins as $admin) {
            try {
                \Illuminate\Support\Facades\Mail::raw(
                    "El reporte de actividad de {$etiq} está listo.\n\n" .
                    "Registros: {$registros->count()}\n" .
                    "Archivo: storage/app/{$path}\n\n— FileCenter",
                    fn($m) => $m->to($admin->email)
                                ->subject("Reporte mensual de actividad {$etiq} — FileCenter")
                );
            } catch (\Exception $e) {
                $this->warn("No se pudo enviar email a {$admin->email}: {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }
}
