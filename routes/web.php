<?php


use Illuminate\Support\Facades\Route;


Route::view('/inicio', 'inicio'); //  http://localhost:8000/inicio

Route::view('/empresas', 'empresas'); // http://localhost:8000/empresas

Route::view('/inversiones', 'inversiones'); // http://localhost:8000/inversiones

Route::view('/liquidaciones', 'liquidaciones'); // http://localhost:8000/liquidaciones

Route::view('/liquidar-proyecto', 'liquidar_proyecto'); // http://localhost:8000/liquidar-proyecto

Route::view('/registro', 'registro'); // http://localhost:8000/registro

Route::view('/simulacion', 'simulacion'); // http://localhost:8000/simulacion

// si tuvieras resources/views/tasas.blade.php
Route::view('/tasas', 'tasas');// GET http://tu-host/tasas



// Home simple
Route::view('/inicio', 'inicio')->name('inicio');

// Fronts “estáticos” (hasta que tengas blades definitivos)
Route::view('/empresas',   'empresas.index')->name('empresas.index')->fallback(function(){});
Route::view('/ubicacion',  'ubicacion.index')->name('ubicacion.index')->fallback(function(){});
Route::view('/usuarios',   'usuarios.index')->name('usuarios.index')->fallback(function(){});
Route::view('/inversiones','inversiones.index')->name('inversiones.index')->fallback(function(){});
Route::view('/tipos-inversion', 'tipos.index')->name('tipos.index')->fallback(function(){});
Route::view('/tasas',      'tasas.index')->name('tasas.index')->fallback(function(){});
Route::view('/simulacion', 'simulacion.index')->name('simulacion.index')->fallback(function(){});
Route::view('/liquidacion','liquidacion.index')->name('liquidacion.index')->fallback(function(){});
Route::view('/consultas',  'consultas.index')->name('consultas.index')->fallback(function(){});

// Cerrar sesión (compat con legacy usando sesión)
Route::get('/logout', function () {
    session()->flush();
    return redirect('/'); // o a tu login
})->name('logout');

