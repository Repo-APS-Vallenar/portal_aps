<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Response;

class SecurityServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Aplicar headers de seguridad globalmente
        Response::macro('addSecurityHeaders', function () {
            $this->header('X-Frame-Options', 'DENY');
            $this->header('X-Content-Type-Options', 'nosniff');
            $this->header('X-XSS-Protection', '1; mode=block');
            $this->header('Referrer-Policy', 'strict-origin-when-cross-origin');
            $this->header('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; frame-ancestors 'none';");
            
            if (app()->environment('production')) {
                $this->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            }
            
            return $this;
        });
    }
}
