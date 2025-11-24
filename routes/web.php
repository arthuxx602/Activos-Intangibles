<?php

use Illuminate\Support\Facades\Route;

// ===== Controladores =====
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\LogoutController;

use App\Http\Controllers\Proyecto\ProjectSelectionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\TipoInversionController;
use App\Http\Controllers\Moderador\HomeController as ModHome;
use App\Http\Controllers\Moderador\UsuarioController as ModUsuarios;
use App\Http\Controllers\Moderador\InversionistaController as ModInversiones;
use App\Http\Controllers\Inversionista\DashboardController as InvDash;
use App\Http\Controllers\Inversionista\ResumenController as InvResumen;
use App\Http\Controllers\Inversionista\ReporteController as InvReporte;
use App\Http\Controllers\Inversionista\LiquidacionController as InvLiquidacion;
// … (el resto de use que ya tienes)

Route::middleware('web')->group(function () {

    // =======================
    //       PÚBLICAS
    // =======================
    Route::get('/', [LandingController::class, 'index'])->name('landing');

    // login/logout SIN middleware de autenticación
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::post('/logout', function () {
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('landing');
    })->name('logout');

    // =======================
    //   RUTAS CON SESIÓN
    // =======================
    Route::middleware('legacy.auth')->group(function () {

        // Rutas de aterrizaje por rol
        Route::get('/admin', [AdminDashboard::class, 'index'])->name('admin.inicio');

        // Selección de proyecto
        Route::get('/proyectos/seleccionar',  [ProjectSelectionController::class, 'index'])
            ->name('proyectos.seleccionar');
        Route::post('/proyectos/seleccionar', [ProjectSelectionController::class, 'store'])
            ->name('proyectos.seleccionar.post');

        // Tipos de inversión protegidos
        Route::resource('/tipos', TipoInversionController::class);

        // Dashboard simple
        Route::get('/dashboard', fn () => redirect()->route('tipos.index'))->name('dashboard');

        // =======================
        //      MODERADOR (rol=2)
        // =======================
        Route::middleware(['legacy.role:2', 'project.selected'])
            ->prefix('moderador')
            ->name('moderador.')
            ->group(function () {
                Route::get('/', [ModHome::class, 'index'])->name('inicio');

                // Usuarios
                Route::get('usuarios',        [ModUsuarios::class, 'index'])->name('usuarios.index');
                Route::post('usuarios',       [ModUsuarios::class, 'store'])->name('usuarios.store');
                Route::put('usuarios/{id}',   [ModUsuarios::class, 'update'])->name('usuarios.update');
                Route::delete('usuarios/{id}',[ModUsuarios::class, 'destroy'])->name('usuarios.destroy');

                // Inversiones moderador
                Route::get('inversiones',  [ModInversiones::class, 'index'])->name('inversiones.index');
                Route::post('inversiones', [ModInversiones::class, 'store'])->name('inversiones.store');

                // Consultas, estadísticas, etc. (como ya lo tienes)
                // ...
            });

        // =======================
        //   INVERSIONISTA (rol=3)
        // =======================
        Route::middleware(['legacy.role:3', 'project.selected'])
            ->prefix('inversionista')
            ->name('inversionista.')
            ->group(function () {
                Route::get('/',                    [InvDash::class, 'index'])->name('inicio');
                Route::get('/resumen',             [InvResumen::class, 'index'])->name('resumen');
                Route::get('/liquidacion',         [InvLiquidacion::class, 'index'])->name('liquidacion.index');
                Route::get('/reportes/line-anual', [InvReporte::class, 'lineAnual'])->name('reportes.line-anual');
            });

        // Otras rutas protegidas (mantenimiento, notas, etc.)
        // ...
    });

    // =======================
    //      FALLBACK 404
    // =======================
    Route::fallback(function () {
        return response()->view('errors.404', [], 404);
    });
});
