<?php

use Illuminate\Support\Facades\Route;

// ===== Controladores =====
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\LogoutController;

use App\Http\Controllers\Proyecto\ProjectSelectionController;

use App\Http\Controllers\TipoInversionController;

use App\Http\Controllers\Moderador\HomeController as ModHome;
use App\Http\Controllers\Moderador\UsuarioController as ModUsuarios;
use App\Http\Controllers\Moderador\InversionController as ModInversiones;
use App\Http\Controllers\Moderador\EstadisticasController as ModStats;
use App\Http\Controllers\Moderador\ConsultasController as ModConsultas;
// use App\Http\Controllers\Moderador\InversionDocumentoController as ModInvDocs; // si existe

use App\Http\Controllers\Inversionista\DashboardController as InvDash;
use App\Http\Controllers\Inversionista\ResumenController as InvResumen;
use App\Http\Controllers\Inversionista\ReporteController as InvReporte;
use App\Http\Controllers\Inversionista\LiquidacionController as InvLiquidacion;
use App\Http\Controllers\Inversionista\InversionistaController;


use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\NotasController;

// ===================================================================
// Todo bajo el middleware de "web" (cookies, sesión, CSRF, etc.)
// ===================================================================
Route::middleware('web')->group(function () {

    // =======================
    //       PÚBLICAS
    // =======================
    Route::get('/', [LandingController::class, 'index'])->name('landing');

    Route::post('/login',  [AuthController::class, 'login'])->name('login');

    // Logout (POST). Alias legacy GET opcional para compatibilidad
    Route::post('/logout', LogoutController::class)->name('logout');
    Route::get('/cerrar-sesion', [LogoutController::class, 'legacy'])->name('logout.legacy');


    // =======================
    //   REQUIEREN SESIÓN
    // =======================
    Route::middleware('legacy.auth')->group(function () {

        // --------- Selección de proyecto (reemplaza eleccionproyecto.php + guardar_proyecto_seleccionado.php)
        Route::get('/proyectos/seleccionar',  [ProjectSelectionController::class, 'index'])
            ->name('proyectos.seleccionar');
        Route::post('/proyectos/seleccionar', [ProjectSelectionController::class, 'store'])
            ->name('proyectos.seleccionar.store');

        // --------- Tipos de inversión (ejemplo de recurso protegido)
        Route::resource('/tipos', TipoInversionController::class);

        // Dashboard simple de aterrizaje tras login
        Route::get('/dashboard', fn () => redirect()->route('tipos.index'))->name('dashboard');
        Route::post('/login', [AuthController::class, 'login'])->name('login');

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

                // Inversiones
                Route::get('inversiones',  [ModInversiones::class, 'index'])->name('inversiones.index');
                Route::post('inversiones', [ModInversiones::class, 'store'])->name('inversiones.store');
                Route::get('/inversionista', [InvDash::class, 'index'])->name('inversionista.inicio');
                // Certificados / carga PDF (si existe el controlador)
                // Route::get('inversiones/registrar',  [ModInvDocs::class, 'create'])->name('inversiones.docs.create');
                // Route::post('inversiones/registrar', [ModInvDocs::class, 'store'])->name('inversiones.docs.store');
                // Route::get('inversiones/docs',       [ModInvDocs::class, 'index'])->name('inversiones.docs.index');

                // Estadísticas (reemplazo de datosLine.php)
                Route::get('estadisticas/datos-line', [ModStats::class, 'datosLine'])->name('estadisticas.datos-line');

                // Consultas
                Route::get('consultas',  [ModConsultas::class, 'index'])->name('consultas.index');
                Route::post('consultas', [ModConsultas::class, 'consultar'])->name('consultas.consultar');
                Route::get('consultas/proyectos', [ModConsultas::class, 'proyectosPorUsuario'])
                    ->name('consultas.proyectos-usuario'); // ?usuario_id=123
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

        // =======================
        //   Otros protegidos
        // =======================
        Route::get('/mantenimiento', [MaintenanceController::class, 'show'])->name('mantenimiento');

        Route::get('/notas',  [NotasController::class, 'create'])->name('notas.create');
        Route::post('/notas', [NotasController::class, 'store'])->name('notas.store');
    });


    // =======================
    //      FALLBACK 404
    // =======================
    Route::fallback(function () {
        return response()->view('errors.404', [], 404);
    });
});
