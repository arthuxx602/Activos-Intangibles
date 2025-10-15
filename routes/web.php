<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inversionista\ReporteController;

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



// Home por defecto
Route::redirect('/', '/dashboard');
