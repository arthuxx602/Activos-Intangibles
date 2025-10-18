<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureLegacyRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if ((int) session('rol') !== (int) $role) {
            abort(403);
        }
        return $next($request);
    }
}
