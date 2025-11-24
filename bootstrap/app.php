<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Middlewares propios
use App\Http\Middleware\EnsureLegacyAuthenticated;
use App\Http\Middleware\EnsureLegacyRole;
use App\Http\Middleware\EnsureProjectSelected;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',         // déjalo si usas API; si no, puedes quitarlo
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // REGISTRA SOLO ALIASES (para usarlos por ruta: middleware('legacy.auth'), etc.)
        $middleware->alias([
            'legacy.auth'      => EnsureLegacyAuthenticated::class,
            'legacy.role'      => EnsureLegacyRole::class,
            'project.selected' => EnsureProjectSelected::class,
        ]);

        // MUY IMPORTANTE: NO los agregues al grupo 'web' globalmente.
        // Asegúrate de NO tener nada como:
        // $middleware->appendToGroup('web', [...]);
        // $middleware->prependToGroup('web', [...]);
        // $middleware->append([...]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Personaliza el manejo de excepciones si lo necesitas
    })
    ->create();
