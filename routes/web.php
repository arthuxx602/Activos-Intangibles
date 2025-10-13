<?php


use Illuminate\Support\Facades\Route;

// VISTAS (Blade)
Route::view('/dashboard', 'dashboard.index')->name('dashboard');   
Route::view('/inicio', 'dashboard.index')->name('inicio');

Route::view('/empresas', 'empresas.index')->name('empresas.index');
Route::view('/inversiones', 'inversiones.index')->name('inversiones.index');

Route::view('/tasas', 'tasas.index')->name('tasas.index');
Route::view('/simulacion', 'simulacion.index')->name('simulacion.index');

Route::view('/liquidacion', 'liquidacion.index')->name('liquidacion.index');
Route::view('/liquidar-proyecto', 'liquidar-proyecto.index')->name('liquidar-proyecto.index'); // 👈 NUEVA

Route::view('/registro', 'registro.index')->name('registro.index'); 
Route::view('/consultas', 'consultas.index')->name('consultas.index');
Route::view('/tipos-inversion', 'tipos-inversion.index')->name('tipos-inversion.index');
Route::view('/ubicacion', 'ubicacion.index')->name('ubicacion.index');
Route::view('/usuarios', 'usuarios.index')->name('usuarios.index');

// Inversionista (vista)
Route::view('/inversionista', 'inversionista.index')->name('inversionista.index');
use App\Http\Controllers\Inversionista\ReporteInversionesController;

Route::middleware(['auth'])->group(function () {
    Route::get('/inversionista/inversiones-anuales', [ReporteInversionesController::class, 'inversionesAnuales'])
        ->name('inversionista.inversiones.anuales');
});



// Home por defecto
Route::redirect('/', '/dashboard');
