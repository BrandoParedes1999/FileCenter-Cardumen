<?php

use App\Http\Controllers\ArchivoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\CarpetaController;
use App\Http\Controllers\PermisoCarpetaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SolicitudAccesoController;
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
    // DASHBOARDS POR ROL
    // ─────────────────────────────────────────

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/admin/dashboard', function () {
        return view('dashboard');
    })->name('admin.dashboard');

    Route::get('/qhse/dashboard', function () {
        return view('dashboard');
    })->name('qhse.dashboard');

    Route::get('/empresa/dashboard', function () {
        return view('dashboard');
    })->name('empresa.dashboard');

    Route::get('/areas', function () {
        return view('areas');
    })->name('areas');

    // ─────────────────────────────────────────
    // CORPORATIVO
    // ─────────────────────────────────────────

    Route::get('/nosotros', function () {
        return view('nosotros');
    })->middleware(['auth', 'verified'])->name('nosotros');

    Route::get('/cardumen', function () {
        return view('cardumen');
    })->name('cardumen');

    // ─────────────────────────────────────────
    // ÁREAS
    // ─────────────────────────────────────────

    Route::get('/seaward', function () {
        return view('seaward');
    })->name('seaward');

    Route::get('/omc', function () {
        return view('omc');
    })->name('omc');

    Route::get('/seatools', function () {
        return view('seatools');
    })->name('seatools');

    Route::get('/tws', function () {
        return view('tws');
    })->name('tws');

    // ─────────────────────────────────────────
    // PERFIL
    // ─────────────────────────────────────────

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
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

    // ─────────────────────────────────────────
    // SOLICITUDES DE ACCESO
    // ─────────────────────────────────────────

    Route::resource('solicitudes', SolicitudAccesoController::class)
        ->only(['index', 'show', 'create', 'store']);

    Route::post('solicitudes/{solicitud}/aprobar', [SolicitudAccesoController::class, 'aprobar'])
        ->name('solicitudes.aprobar');

    Route::post('solicitudes/{solicitud}/rechazar', [SolicitudAccesoController::class, 'rechazar'])
        ->name('solicitudes.rechazar');

    // ─────────────────────────────────────────
    // USUARIOS (CRUD)
    // ─────────────────────────────────────────

    Route::resource('usuarios', UsuarioController::class);

    Route::post('usuarios/{usuario}/toogle-activo', [UsuarioController::class, 'toggleActivo'])
        ->name('usuarios.toogle-activo');

    Route::post('usuarios/{usuario}/desbloquear', [UsuarioController::class, 'desbloquear'])
        ->name('usuarios.desbloquear');

    // ─────────────────────────────────────────
    // PERMISOS DE CARPETA
    // ─────────────────────────────────────────

    Route::prefix('carpetas/{carpeta}')->name('permisos.')->group(function () {
        Route::get('permisos', [PermisoCarpetaController::class, 'index'])->name('index');
        Route::post('permisos', [PermisoCarpetaController::class, 'store'])->name('store');
        Route::put('permisos/{permiso}', [PermisoCarpetaController::class, 'update'])->name('update');
        Route::delete('permisos/{permiso}', [PermisoCarpetaController::class, 'destroy'])->name('destroy');
    });

});

require __DIR__.'/auth.php';