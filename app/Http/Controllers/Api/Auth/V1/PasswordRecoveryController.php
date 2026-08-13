<?php

namespace App\Http\Controllers\Api\Auth\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordRecovery\ForgotPasswordRequest;
use App\Http\Requests\Auth\PasswordRecovery\ResetPasswordRequest;
use App\Services\Auth\V1\PasswordRecoveryService;
use Illuminate\Support\Facades\Response;

class PasswordRecoveryController extends Controller
{
    public function forgot(ForgotPasswordRequest $request, PasswordRecoveryService $service)
    {
        $service->requestRecovery($request->validated()['email']);

        return Response::api(null, 'If the e-mail exists, a recovery link has been sent.');
    }

    public function reset(ResetPasswordRequest $request, PasswordRecoveryService $service)
    {
        $data = $request->validated();

        $service->resetPassword($data['token'], $data['password']);

        return Response::api(null, 'Password updated successfully.');
    }
}
