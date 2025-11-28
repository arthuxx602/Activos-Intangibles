<?php

/**
 * RUTAS DE DEBUGGING - ELIMINA ESTAS RUTAS EN PRODUCCIÓN
 * Úsalas para verificar el estado de la sesión durante el desarrollo
 */

use Illuminate\Support\Facades\Route;

Route::get('/debug/sesion', function () {
    return response()->json([
        'authenticated' => session('authenticated'),
        'cedula' => session('cedula'),
        'rol' => session('rol'),
        'nombre' => session('nombre'),
        'apellido' => session('apellido'),
        'proyecto_seleccionado' => session('proyecto_seleccionado'),
        'nombre_proyecto' => session('nombre_proyecto'),
        'all_session' => session()->all(),
    ]);
})->name('debug.sesion');

Route::get('/debug/limpiar', function () {
    session()->invalidate();
    session()->regenerateToken();
    return redirect('/')->with('status', 'Sesión limpiada');
})->name('debug.limpiar');
