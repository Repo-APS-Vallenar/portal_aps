<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        'tickets/*/documents',
        'test-rate-limit',  // Ruta de prueba para rate limiting
    ];
} 