<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureProjectSelected
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('proyecto_seleccionado')) {
            return redirect()->route('proyectos.seleccionar');
        }
        return $next($request);
    }
}
