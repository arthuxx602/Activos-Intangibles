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
        api: __DIR__ . '/../routes/api.php',       // quítalo si no usas API o no tienes routes/api.php
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Alias para usarlos en las rutas: 'legacy.auth', 'legacy.role:2', 'project.selected'
        $middleware->alias([
            'legacy.auth'      => EnsureLegacyAuthenticated::class,
            'legacy.role'      => EnsureLegacyRole::class,
            'project.selected' => EnsureProjectSelected::class,
        ]);

        // (Opcional) Si quieres que alguno corra siempre en el grupo "web":
        // $middleware->appendToGroup('web', [
        //     EnsureProjectSelected::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Personaliza el manejo de excepciones si lo necesitas
    })
    ->create();
