<?php

use Illuminate\Support\Facades\Route;

// Ruta API para prueba de rate limiting (SIN CSRF, SIN SESIONES)
Route::post('/test-rate-limit', function () {
    return response()->json([
        'message' => 'Rate limit test API',
        'timestamp' => now()->toISOString(),
        'ip' => request()->ip(),
        'user_agent' => request()->userAgent()
    ]);
})->middleware('throttle:3,1'); // 3 intentos por minuto 