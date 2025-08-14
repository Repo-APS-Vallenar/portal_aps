<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class AddSecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // No aplicar headers de seguridad a descargas de archivos o respuestas streamed
        if ($response instanceof StreamedResponse || 
            $response instanceof BinaryFileResponse ||
            $response->headers->get('Content-Disposition')) {
            return $response;
        }
        
        // Aplicar headers de seguridad solo si no se han enviado ya y la respuesta tiene el método header
        if (!headers_sent() && method_exists($response, 'header')) {
            $response->header('X-Frame-Options', 'DENY');
            $response->header('X-Content-Type-Options', 'nosniff');
            $response->header('X-XSS-Protection', '1; mode=block');
            
            // CSP desactivado temporalmente para debug
            // $response->header('Content-Security-Policy', $csp);
            
            $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');
            $response->header('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        }
        
        return $response;
    }
}
