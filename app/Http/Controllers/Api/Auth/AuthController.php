<?php
namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email'    => ['Email Invalido'],
                'password' => ['Senha Invalida'],
            ]);
        }

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return Response::api(
            ['user' => $request->user()],
            'Login realizado com sucesso'
        );
    }

    public function me(Request $request)
    {
        return Response::api(['user' => $request->user()]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        if ($request->hasSession()) {
            $request->session()->invalidate();

            $request->session()->regenerateToken();
        }

        return Response::api(null, 'Logout realizado com sucesso');
    }
}
