<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index(Request $request)
    {
        // Si ya está “logueado” en sesión, redirigimos por rol
        if (session('authenticated') === true) {
            return $this->redirigirPorRol(session('rol'));
        }
        return view('landing.index');
    }

    private function redirigirPorRol($rolId)
    {
        switch ((int)$rolId) {
            case 1: return redirect()->route('admin.inicio');
            case 2: return redirect()->route('moderador.inicio');
            case 3: return redirect()->route('inversionista.inicio');
            default: return redirect()->route('landing');
        }
    }
}
