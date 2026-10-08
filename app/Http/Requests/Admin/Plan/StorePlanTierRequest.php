<?php

namespace App\Http\Requests\Admin\Plan;

class StorePlanTierRequest extends BasePlanTierRequest
{
    public function rules(): array
    {
        $rules = $this->commonRules();

        $rules['name'][] = 'required';
        $rules['base_price'][] = 'required';
        $rules['max_employees'][] = 'required';
        $rules['modules'][] = 'required';

        return $rules;
    }
}
