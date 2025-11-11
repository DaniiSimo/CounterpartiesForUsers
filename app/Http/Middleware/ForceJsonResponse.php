<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
/**
 * Посредник, предоставляющий ответы в виде JSON
 */
class ForceJsonResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set(key: 'Accept', values: 'application/json');
        return $next($request);
    }
}
