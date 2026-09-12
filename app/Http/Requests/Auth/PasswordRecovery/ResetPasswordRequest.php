<?php

namespace App\Http\Requests\Auth\PasswordRecovery;

use App\Http\Requests\Base\PublicFormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends PublicFormRequest
{
    public function rules(): array
    {
        return [
            'token' => ['required', 'string', 'uuid', 'max:255'],
            'password' => [
                'required',
                'string',
                Password::min(10)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
                'confirmed',
            ],
        ];
    }
}
