<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Limpieza diaria de archivos temporales de solicitudes de subida procesadas
Schedule::command('filecenter:limpiar-temporales --dias=7')
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/limpieza-temporales.log'));

// Reporte mensual automático: último día del mes a las 23:00
// Se genera ANTES de la limpieza de logs
Schedule::command('filecenter:reporte-mensual')
    ->lastDayOfMonth('23:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/reporte-mensual.log'));

// Limpieza de logs: último día del mes a las 23:30 (30 min después del reporte)
Schedule::command('filecenter:limpiar-logs --dias=90')
    ->lastDayOfMonth('23:30')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/limpieza-logs.log'));
