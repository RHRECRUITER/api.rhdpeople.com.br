<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Verificar se usuário está autenticado
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Se não for super admin, verificar se empresa está ativa
        $user = auth()->user();
        
        if ($user->tipo !== 'super_admin') {
            if (!$user->empresa_id) {
                auth()->logout();
                return redirect()->route('login')
                    ->with('error', 'Usuário sem empresa vinculada');
            }

            if (!$user->empresa->isAtiva()) {
                auth()->logout();
                return redirect()->route('login')
                    ->with('error', 'Empresa inativa ou suspensa');
            }
        }

        return $next($request);
    }
}
