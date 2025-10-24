<?php

namespace App\Http\Controllers;

use App\Models\LogAcesso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Buscar usuário
        $user = \App\Models\User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->with('error', 'Credenciais inválidas');
        }

        // Verificar se usuário está ativo
        if (!$user->ativo) {
            return back()->with('error', 'Usuário inativo');
        }

        // Verificar se empresa está ativa (exceto super admin)
        if ($user->tipo !== 'super_admin') {
            if (!$user->empresa_id || !$user->empresa->isAtiva()) {
                return back()->with('error', 'Empresa inativa ou suspensa');
            }
        }

        // Login
        Auth::login($user, $request->filled('remember'));

        // Atualizar último acesso
        $user->update(['ultimo_acesso' => now()]);

        // Registrar log
        LogAcesso::create([
            'user_id' => $user->id,
            'empresa_id' => $user->empresa_id,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'acao' => 'login',
        ]);

        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    public function logout(Request $request)
    {
        // Registrar log
        if (Auth::check()) {
            LogAcesso::create([
                'user_id' => Auth::id(),
                'empresa_id' => Auth::user()->empresa_id,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'acao' => 'logout',
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
