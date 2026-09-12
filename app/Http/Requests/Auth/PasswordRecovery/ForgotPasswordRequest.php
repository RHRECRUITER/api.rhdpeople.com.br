<?php

namespace App\Http\Requests\Auth\PasswordRecovery;

use App\Http\Requests\Base\PublicFormRequest;

class ForgotPasswordRequest extends PublicFormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255'],
        ];
    }
}
