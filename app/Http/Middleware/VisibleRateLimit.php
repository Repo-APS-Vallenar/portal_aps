<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpFoundation\Response;

class VisibleRateLimit
{
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle(Request $request, Closure $next, int $maxAttempts = 5, int $decayMinutes = 1): Response
    {
        $key = $this->resolveRequestSignature($request);
        
        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            $response = response()->json([
                'error' => 'Too Many Requests',
                'message' => 'Rate limit exceeded. Please try again later.',
                'retry_after' => $this->limiter->availableIn($key)
            ], 429);
            
            $response->header('X-RateLimit-Limit', $maxAttempts);
            $response->header('X-RateLimit-Remaining', 0);
            $response->header('Retry-After', $this->limiter->availableIn($key));
            
            return $response;
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        $response->header('X-RateLimit-Limit', $maxAttempts);
        $response->header('X-RateLimit-Remaining', max(0, $maxAttempts - $this->limiter->attempts($key)));

        return $response;
    }

    protected function resolveRequestSignature(Request $request): string
    {
        return sha1($request->method() . '|' . $request->server('SERVER_NAME') . '|' . $request->path() . '|' . $request->ip());
    }
}
