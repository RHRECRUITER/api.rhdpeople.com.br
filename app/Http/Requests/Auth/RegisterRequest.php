<?php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // User Data
            'name'                   => ['required', 'string', 'max:255'],
            'email'                  => ['required', 'email', 'unique:users,email'],
            'password'               => ['required', 'string', 'min:8'],

            // Company Data
            'company.name'           => ['required', 'string'],
            'company.cnpj'           => ['required', 'string', 'unique:companies,document'], //verificar se necessario ajuste
            'company.email'          => ['required', 'email'],
            'company.phone'          => ['required', 'string'],

                                                              // Plan Data
            'plan.plan_tier_id'      => ['required', 'uuid'], //definição temporaria ate implementação final
            'plan.employee_range_id' => ['required', 'uuid'], //definição temporaria ate implementação final
            'plan.price_total'       => ['required', 'numeric'],
        ];
    }
}
