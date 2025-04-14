<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureSuperadmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->role !== 'superadmin') {
            abort(403, 'Acceso no autorizado.');
        }

        return $next($request);
    }
}
