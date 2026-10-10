<?php

namespace App\Http\Requests\Guest;

use App\Enums\TypeLicense;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InviteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'unique:users', 'max:200'],
            'type_license' => ['required', Rule::enum(TypeLicense::class)->only([
                TypeLicense::OPERACIONAL,
                TypeLicense::COLABORADOR,
            ])],
            'expiration_date' => ['required, date'],
            'license_id' => ['required', 'uuid', 'exists:license,id'],
            'id_plan' => ['required', 'uuid', 'exists:plan,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'email já registrado no banco de dados.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nome completo',
            'type_license' => 'tipo de licença',
            'email' => 'Endereço de e-mail',
        ];
    }
}
