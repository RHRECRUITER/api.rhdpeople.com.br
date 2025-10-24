<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  array|string  $roles
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userRole = auth()->user()->tipo;

        if (!in_array($userRole, $roles)) {
            abort(403, 'Acesso negado. Você não tem permissão para acessar esta página.');
        }

        return $next($request);
    }
}
