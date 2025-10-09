return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // 
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware()
    ->withExceptions()
    ->create();
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\ProyectoController;
use App\Http\Controllers\Admin\InversionController;

// Ruta de prueba
Route::get('/test', fn() => response()->json(['status' => 'API funcionando ✅']));

// Usuarios
Route::get('/usuarios', [UsuarioController::class, 'index']);
Route::post('/usuarios', [UsuarioController::class, 'store']);
Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);
Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);

// Proyectos
Route::get('/proyectos', [ProyectoController::class, 'index']);
Route::post('/proyectos', [ProyectoController::class, 'store']);
Route::put('/proyectos/{id}', [ProyectoController::class, 'update']);
Route::delete('/proyectos/{id}', [ProyectoController::class, 'destroy']);
use App\Http\Controllers\Admin\{
    EmpresaController, PaisController, DepartamentoController, MunicipioController,
    TipoInversionController, TasaController, UbicacionController, VinculacionController,
    LiquidacionController, SimulacionController, ConsultaController, ReporteController, DescargaController
};

Route::apiResources([
    'empresas'        => EmpresaController::class,
    'paises'          => PaisController::class,
    'departamentos'   => DepartamentoController::class,
    'municipios'      => MunicipioController::class,
    'tipos-inversion' => TipoInversionController::class,
    'tasas'           => TasaController::class,
    'ubicaciones'     => UbicacionController::class,
    'vinculaciones'   => VinculacionController::class,
    'liquidaciones'   => LiquidacionController::class,
    'simulaciones'    => SimulacionController::class,
]);

Route::get('reportes/datos-line', [ReporteController::class, 'datosLine']);
Route::get('consultas/busqueda',  [ConsultaController::class, 'busqueda']);
Route::get('descargas/inversiones', [DescargaController::class, 'inversiones']);
Route::get('descargas/proyectos',   [DescargaController::class, 'proyectos']);

<?php


use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\ProyectoController;
use App\Http\Controllers\Admin\InversionController;

use App\Http\Controllers\Admin\EmpresaController;
use App\Http\Controllers\Admin\PaisController;
use App\Http\Controllers\Admin\DepartamentoController;
use App\Http\Controllers\Admin\MunicipioController;
use App\Http\Controllers\Admin\TipoInversionController;
use App\Http\Controllers\Admin\TasaController;
use App\Http\Controllers\Admin\UbicacionController;
use App\Http\Controllers\Admin\VinculacionController;
use App\Http\Controllers\Admin\LiquidacionController;
use App\Http\Controllers\Admin\SimulacionController;
use App\Http\Controllers\Admin\ConsultaController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\Admin\DescargaController;

// Ping de salud
Route::get('/test', fn () => response()->json(['status' => 'API funcionando ✅']));

// Recursos REST (CRUD)
Route::apiResources([
    'usuarios'        => UsuarioController::class,
    'proyectos'       => ProyectoController::class,
    'inversiones'     => InversionController::class,
    'empresas'        => EmpresaController::class,
    'paises'          => PaisController::class,
    'departamentos'   => DepartamentoController::class,
    'municipios'      => MunicipioController::class,
    'tipos-inversion' => TipoInversionController::class,
    'tasas'           => TasaController::class,
    'ubicaciones'     => UbicacionController::class,
    'vinculaciones'   => VinculacionController::class,
    'liquidaciones'   => LiquidacionController::class,
    'simulaciones'    => SimulacionController::class,
]);

// Acciones específicas
Route::post('proyectos/{proyecto}/liquidar', [ProyectoController::class, 'liquidar']);
Route::post('proyectos/{proyecto}/simular',  [ProyectoController::class, 'simular']);

// Consultas y reportes
Route::get('consultas/busqueda',  [ConsultaController::class, 'busqueda']);
Route::get('reportes/datos-line', [ReporteController::class, 'datosLine']);

// Descargas
Route::get('descargas/inversiones', [DescargaController::class, 'inversiones']);
Route::get('descargas/proyectos',   [DescargaController::class, 'proyectos']);


Route::get('consultas/usuarios', [ConsultaController::class, 'usuarios']);
Route::get('consultas/proyectos-por-usuario/{usuario}', [ConsultaController::class, 'proyectosPorUsuario']);
Route::get('consultas/resumen', [ConsultaController::class, 'resumen']);
Route::get('consultas/busqueda', [ConsultaController::class, 'busqueda']); // opcional
