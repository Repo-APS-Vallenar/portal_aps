<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Cache;

class LogFailedLogin
{
    public function handle(Failed $event)
    {
        $email = $event->credentials['email'] ?? 'unknown';

        // Clave para el cache (contador por usuario)
        $key = 'login_attempts:' . $email;

        // Incrementar el contador de intentos
        Cache::increment($key);

        // Establecer vencimiento (por ejemplo, 15 minutos)
        Cache::put($key, Cache::get($key), now()->addMinutes(15));
    }
}
