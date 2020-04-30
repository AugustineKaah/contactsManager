<?php

namespace App\Http\Middleware;

use Closure;

class CORS
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request)
        ->header('Access-Control-Allow-Orijin', '*')
        ->header('Access-Control-Allow-Methods', 'PUT, GET, POST, DELETE, OPTIONS, PATCH')
        ->header('Access-Control-Allow-Headers', 'Orijin, Content-Type, Accept, Authorization, x-request-With, cache-control')
        ->header('Access-Conrol-Allow-Credentials', 'true');
    }
}
