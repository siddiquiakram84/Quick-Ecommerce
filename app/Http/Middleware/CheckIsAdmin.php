<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user() && auth()->user()->hasRole('admin')) {
            return $next($request);
        }
    
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
// public function handle(Request $request, Closure $next)
// {
//     if (auth()->user() && auth()->user()->hasRole('admin')) {
//         return $next($request);
//     }

//     return response()->json(['error' => 'Unauthorized'], 401);
// }
