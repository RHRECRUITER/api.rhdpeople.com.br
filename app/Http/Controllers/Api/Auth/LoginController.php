<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\AuthenticationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class LoginController extends Controller
{
    public function __construct(
        private AuthenticationService $authService
    ) {}

    public function store(LoginRequest $request): JsonResponse
    {
        $this->authService->authenticate($request->only('email', 'password'));

        $request->session()->regenerate();

        $payload = $this->authService->buildLoginResponse(Auth::user());

        return Response::api([
            'success' => true,
            'message' => 'Login realizado com sucesso',
            'data' => $payload,
        ], 200);
    }

    public function destroy(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Response::api([
            'sucess' => true,
            'message' => 'Logout realizado com sucesso',
        ], 200);
    }
}
