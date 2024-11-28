<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Verifica si el usuario está autenticado y tiene rolID = 3
        if (Auth::check() && Auth::user()->rolID == 2) {
            return $next($request);
        }

        // Si no cumple, redirige al usuario (por ejemplo, a una página de error)
        return redirect('/')->with('error', 'No tienes acceso a esta página.');
    }
}
