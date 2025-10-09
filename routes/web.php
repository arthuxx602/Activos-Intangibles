<?php


use Illuminate\Support\Facades\Route;


Route::view('/inicio', 'inicio'); //  http://localhost:8000/inicio

Route::view('/empresas', 'empresas'); // http://localhost:8000/empresas

Route::view('/inversiones', 'inversiones'); // http://localhost:8000/inversiones

Route::view('/liquidaciones', 'liquidaciones'); // http://localhost:8000/liquidaciones

Route::view('/liquidar-proyecto', 'liquidar_proyecto'); // http://localhost:8000/liquidar-proyecto

Route::view('/registro', 'registro'); // http://localhost:8000/registro

Route::view('/simulacion', 'simulacion'); // http://localhost:8000/simulacion
