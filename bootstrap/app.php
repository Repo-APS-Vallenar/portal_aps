<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware global de headers de seguridad
        $middleware->web(append: [
            \App\Http\Middleware\AddSecurityHeaders::class,
        ]);
        
        // Registrar middleware de alias
        $middleware->alias([
            'check.ownership' => \App\Http\Middleware\CheckResourceOwnership::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
