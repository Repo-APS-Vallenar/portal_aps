<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || (auth()->user()->role !== 'admin' && auth()->user()->role !== 'superadmin')) {
            return redirect()->route('home')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
} 