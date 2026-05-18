<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\ArchivoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\CarpetaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\SolicitudAccesoController;
use App\Http\Controllers\SolicitudSubidaController;
use App\Http\Controllers\PermisoCarpetaController;
use App\Http\Controllers\PermisosGlobalesController;
use App\Http\Controllers\BuscadorController;
use App\Http\Controllers\ReporteActividadController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\PapeleraController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────
// PÁGINA DE INICIO
// ─────────────────────────────────────────────

Route::get('/', function () {
    return redirect()->route('login');
});

// ─────────────────────────────────────────────
// RUTAS PROTEGIDAS
// ─────────────────────────────────────────────

Route::middleware(['auth', 'company.scope'])->group(function () {

    // ─────────────────────────────────────────
    // DASHBOARDS
    // ─────────────────────────────────────────

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // ─────────────────────────────────────────
    // CORPORATIVO
    // ─────────────────────────────────────────

    Route::get('/nosotros', function () {
        return view('nosotros');
    })->middleware(['auth', 'verified'])->name('nosotros');

    // ─────────────────────────────────────────
    // ÁREAS
    // ─────────────────────────────────────────

    Route::get('/areas',           [AreaController::class, 'index'])->name('areas.index');
    Route::get('/areas/{empresa}', [AreaController::class, 'show'])->name('areas.show');

    // ─────────────────────────────────────────
    // PERFIL
    // ─────────────────────────────────────────

    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ─────────────────────────────────────────
    // CARPETAS
    // ─────────────────────────────────────────

    Route::resource('carpetas', CarpetaController::class);

    // ─────────────────────────────────────────
    // ARCHIVOS
    // ─────────────────────────────────────────

    Route::resource('archivos', ArchivoController::class);

    Route::get('archivos/{archivo}/descargar', [ArchivoController::class, 'descargar'])
        ->name('archivos.descargar');

    Route::post('archivos/{archivo}/restaurar-version', [ArchivoController::class, 'restaurarVersion'])
        ->name('archivos.restaurar-version');

    Route::get('archivos/{archivo}/ver',      [ArchivoController::class, 'ver'])
        ->name('archivos.ver');
    Route::get('archivos/{archivo}/contenido',[ArchivoController::class, 'contenido'])
        ->name('archivos.contenido');

    Route::get('archivos/{archivo}/mover',  [ArchivoController::class, 'moverForm'])
        ->name('archivos.mover.form');
    Route::post('archivos/{archivo}/mover', [ArchivoController::class, 'mover'])
        ->name('archivos.mover');

    // ─────────────────────────────────────────
    // SOLICITUDES DE ACCESO (cross-empresa)
    // ─────────────────────────────────────────

    Route::resource('solicitudes', SolicitudAccesoController::class)
        ->only(['index', 'show', 'create', 'store']);

    Route::post('solicitudes/{solicitud}/aprobar', [SolicitudAccesoController::class, 'aprobar'])
        ->name('solicitudes.aprobar');

    Route::post('solicitudes/{solicitud}/rechazar', [SolicitudAccesoController::class, 'rechazar'])
        ->name('solicitudes.rechazar');

    // ─────────────────────────────────────────
    // SOLICITUDES DE SUBIDA (aprobación interna)
    // ─────────────────────────────────────────
    // Cuando Auxiliar/Empleado quieren subir a una carpeta
    // con requiere_aprobacion_subida = true, el archivo queda
    // en estado Pendiente hasta que Admin/Gerente lo apruebe.

    Route::get('solicitudes-subida', [SolicitudSubidaController::class, 'index'])
        ->name('solicitudes-subida.index');

    Route::get('solicitudes-subida/{solicitudSubida}', [SolicitudSubidaController::class, 'show'])
        ->name('solicitudes-subida.show');

    Route::post('solicitudes-subida/{solicitudSubida}/aprobar', [SolicitudSubidaController::class, 'aprobar'])
        ->name('solicitudes-subida.aprobar');

    Route::post('solicitudes-subida/{solicitudSubida}/rechazar', [SolicitudSubidaController::class, 'rechazar'])
        ->name('solicitudes-subida.rechazar');

    // ─────────────────────────────────────────
    // USUARIOS (CRUD)
    // ─────────────────────────────────────────

    Route::resource('usuarios', UsuarioController::class);

    Route::post('usuarios/{usuario}/toggle-activo', [UsuarioController::class, 'toggleActivo'])
        ->name('usuarios.toggle-activo');

    Route::post('usuarios/{usuario}/desbloquear', [UsuarioController::class, 'desbloquear'])
        ->name('usuarios.desbloquear');

    // ─────────────────────────────────────────
    // PERMISOS DE CARPETA
    // ─────────────────────────────────────────

    Route::prefix('carpetas/{carpeta}')->name('permisos.')->group(function () {
        Route::get('permisos',              [PermisoCarpetaController::class, 'index'])->name('index');
        Route::post('permisos',             [PermisoCarpetaController::class, 'store'])->name('store');
        Route::put('permisos/{permiso}',    [PermisoCarpetaController::class, 'update'])->name('update');
        Route::delete('permisos/{permiso}', [PermisoCarpetaController::class, 'destroy'])->name('destroy');
    });

    // ─────────────────────────────────────────
    // EMPRESAS
    // ─────────────────────────────────────────

    Route::resource('empresas', EmpresaController::class);

    Route::post('empresas/{empresa}/toggle-activo', [EmpresaController::class, 'toggleActivo'])
        ->name('empresas.toggle-activo');


    // ─────────────────────────────────────────────
    // PERMISOS GLOBALES (vista centralizada)
    // ─────────────────────────────────────────────

    Route::get('permisos-globales', [PermisosGlobalesController::class, 'index'])
        ->name('permisos.global');

    Route::put('permisos-globales/{permiso}', [PermisosGlobalesController::class, 'update'])
        ->name('permisos.global.update');

    Route::delete('permisos-globales/{permiso}', [PermisosGlobalesController::class, 'destroy'])
        ->name('permisos.global.destroy');


    Route::get('/actualizaciones-recientes', [DashboardController::class, 'actualizaciones'])
    ->name('actualizaciones.recientes');

    Route::get('buscador-index', [BuscadorController::class, 'index'])
        ->name('buscar');

    // ─────────────────────────────────────────
    // REPORTES DE ACTIVIDAD (Solo Superadmin / Aux_QHSE)
    // ─────────────────────────────────────────

    Route::get('reportes/actividad', [ReporteActividadController::class, 'index'])
        ->name('reportes.actividad');

    Route::get('reportes/actividad/exportar', [ReporteActividadController::class, 'exportar'])
        ->name('reportes.actividad.exportar');

    // ─────────────────────────────────────────
    // NOTIFICACIONES IN-APP
    // ─────────────────────────────────────────

    Route::get('notificaciones', [NotificacionController::class, 'index'])
        ->name('notificaciones.index');
    Route::post('notificaciones/{id}/leer', [NotificacionController::class, 'marcarLeida'])
        ->name('notificaciones.leer');
    Route::post('notificaciones/leer-todas', [NotificacionController::class, 'marcarTodasLeidas'])
        ->name('notificaciones.leer-todas');
    Route::delete('notificaciones/{id}', [NotificacionController::class, 'destroy'])
        ->name('notificaciones.destroy');
    Route::delete('notificaciones', [NotificacionController::class, 'vaciar'])
        ->name('notificaciones.vaciar');

    // ─────────────────────────────────────────
    // PAPELERA DE RECICLAJE
    // ─────────────────────────────────────────

    Route::get('papelera', [PapeleraController::class, 'index'])
        ->name('papelera.index');
    Route::post('papelera/archivos/{id}/restaurar', [PapeleraController::class, 'restaurarArchivo'])
        ->name('papelera.archivos.restaurar');
    Route::post('papelera/carpetas/{id}/restaurar', [PapeleraController::class, 'restaurarCarpeta'])
        ->name('papelera.carpetas.restaurar');
    Route::delete('papelera/archivos/{id}', [PapeleraController::class, 'eliminarArchivo'])
        ->name('papelera.archivos.destroy');
    Route::delete('papelera/carpetas/{id}', [PapeleraController::class, 'eliminarCarpeta'])
        ->name('papelera.carpetas.destroy');
    Route::delete('papelera', [PapeleraController::class, 'vaciar'])
        ->name('papelera.vaciar');

    // ─────────────────────────────────────────
    // 2FA
    // ─────────────────────────────────────────

    Route::get('two-factor/setup',   [TwoFactorController::class, 'showSetup'])->name('two-factor.setup');
    Route::post('two-factor/enable', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::delete('two-factor',      [TwoFactorController::class, 'disable'])->name('two-factor.disable');
});

// 2FA challenge (fuera del grupo auth para usuarios no logueados aún)
Route::get('two-factor/challenge',  [TwoFactorController::class, 'showChallenge'])->name('two-factor.challenge')->middleware('throttle:10,1');
Route::post('two-factor/challenge', [TwoFactorController::class, 'challenge'])->name('two-factor.verify')->middleware('throttle:5,1');

require __DIR__.'/auth.php';