<?php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }
    public function bodyParameters(): array
    {
        return
            [
            'email'    => [
                'description' => 'E-mail do usuário',
                'example'     => 'teste@gmail.com',
            ],
            'password' => [
                'description' => 'Senha do usuário',
                'example'     => 'Senha@123',
            ],
        ];
    }
    protected function prepareForValidation()
    {
        $this->merge(['email' => mb_strtolower(trim($this->input('email')))]);
    }
}
