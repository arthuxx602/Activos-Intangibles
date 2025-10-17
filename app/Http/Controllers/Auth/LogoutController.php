<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    // POST /logout (recomendado)
    public function __invoke(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
        }

        // limpiar la sesión y regenerar el token CSRF
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // ajusta a tu ruta de portada (usa '/' si no tienes 'landing')
        return redirect()->route('landing', [], false) ?? redirect('/');
    }

    // GET /cerrar-sesion (opcional, para compatibilidad con enlaces viejos)
    public function legacy(Request $request)
    {
        return $this($request);
    }
}
