<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TipoInversionController;
use App\Http\Controllers\Inversionista\ReporteController;
use App\Http\Controllers\Moderador\UsuarioController;
use App\Http\Controllers\Moderador\InversionController;
use App\Http\Controllers\Moderador\HomeController;
use App\Http\Controllers\Moderador\EstadisticasController;
use App\Http\Controllers\Moderador\ConsultasController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Auth\AuthController;
// VISTAS (Blade)
Route::view('/dashboard', 'dashboard.index')->name('dashboard');   
Route::view('/inicio', 'dashboard.index')->name('inicio');

Route::view('/empresas', 'empresas.index')->name('empresas.index');
Route::view('/inversiones', 'inversiones.index')->name('inversiones.index');

Route::view('/tasas', 'tasas.index')->name('tasas.index');
Route::view('/simulacion', 'simulacion.index')->name('simulacion.index');

Route::view('/liquidacion', 'liquidacion.index')->name('liquidacion.index');
Route::view('/liquidar-proyecto', 'liquidar-proyecto.index')->name('liquidar-proyecto.index'); 

Route::view('/registro', 'registro.index')->name('registro.index'); 
Route::view('/consultas', 'consultas.index')->name('consultas.index');
Route::view('/tipos-inversion', 'tipos-inversion.index')->name('tipos-inversion.index');


Route::middleware('auth')->group(function () {
    Route::resource('tipos', TipoInversionController::class);
    Route::get('/', fn() => redirect()->route('tipos.index'))->name('dashboard');
});

Route::view('/ubicacion', 'ubicacion.index')->name('ubicacion.index');
Route::view('/usuarios', 'usuarios.index')->name('usuarios.index');

// Inversionista (vista)
Route::view('/inversionista', 'inversionista.index')->name('inversionista.index');
use App\Http\Controllers\Inversionista\DashboardController as InvDash;
use App\Http\Controllers\Inversionista\ResumenController;
use App\Http\Controllers\Inversionista\ReporteInversionesController;
use App\Http\Controllers\Inversionista\LiquidacionController as InvLiquidacion;

Route::get('/inversionista', [InvDash::class, 'index'])->name('inversionista.index');

Route::get('/inversionista/liquidacion', [InvLiquidacion::class, 'index'])
    ->name('inversionista.liquidacion.index');

Route::get('/inversionista/resumen', [ResumenController::class, 'index'])
    ->name('inversionista.resumen');
// Vista del reporte anual
Route::get('/inversionista/reportes/line-anual', [ReporteController::class, 'lineAnual'])
    ->name('inversionista.reportes.line-anual');

    //vista moderador 
Route::middleware('auth')
  ->prefix('moderador')
  ->name('moderador.')
  ->group(function () {
      // Usuarios 
      Route::get('usuarios',  [UsuarioController::class, 'index'])->name('usuarios.index');
      Route::post('usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');

      // Inversiones
      Route::get('inversiones',  [InversionController::class, 'index'])->name('inversiones.index');
      Route::post('inversiones', [InversionController::class, 'store'])->name('inversiones.store');

     // Form para registrar inversión con PDF (reemplazo de registrarM para inversiones)
      Route::get('inversiones/registrar',  [InversionDocumentoController::class, 'create'])->name('inversiones.docs.create');
      Route::post('inversiones/registrar', [InversionDocumentoController::class, 'store'])->name('inversiones.docs.store');
     // Listado de inversiones con certificado (inversion2)
      Route::get('inversiones/docs', [InversionDocumentoController::class, 'index'])->name('inversiones.docs.index');
    // ruta home controlador 
    Route::get('/', [HomeController::class, 'index'])->name('inicio');
    //ruta datos 
    // Endpoint JSON (reemplaza datosLine.php)
    Route::get('estadisticas/datos-line', [EstadisticasController::class, 'datosLine'])
         ->name('estadisticas.datos-line');
    // Página principal de consultas (form + resultados)
      Route::get('consultas',  [ConsultasController::class, 'index'])->name('consultas.index');
      Route::post('consultas', [ConsultasController::class, 'consultar'])->name('consultas.consultar');
    // AJAX: proyectos por usuario (devuelve <option>…)
      Route::get('consultas/proyectos', [ConsultasController::class, 'proyectosPorUsuario'])
           ->name('consultas.proyectos-usuario'); // ?usuario_id=123

  
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Login / Logout
Route::post('/login',  [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// (Estas rutas deben existir en la app; ajústarlas si usan otros nombres)
Route::get('/admin/inicio',       fn()=>view('admin.inicio'))->name('admin.inicio');
Route::get('/moderador/inicio',   fn()=>view('moderador.inicio'))->name('moderador.inicio');
Route::get('/inversionista/inicio', fn()=>view('inversionista.inicio'))->name('inversionista.inicio');

// Elección de proyecto (si se sigue con el proyecto 
Route::get('/proyecto/seleccionar', fn()=>view('proyecto.eleccion'))->name('proyecto.elegir');


  });


// Home por defecto
Route::redirect('/', '/dashboard');
