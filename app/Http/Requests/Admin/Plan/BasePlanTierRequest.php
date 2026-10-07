<?php

namespace App\Http\Requests\Admin\Plan;

use App\Http\Requests\Base\PublicFormRequest;

abstract class BasePlanTierRequest extends PublicFormRequest
{
    protected function commonRules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'base_price' => ['numeric', 'min:0'],
            'max_employees' => ['nullable', 'integer', 'min:1'],
            'annual_discount_pct' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['boolean'],

            // Validação dos Arrays Aninhados
            'modules' => ['nullable', 'array'],
            'modules.*.module_id' => ['required_with:modules', 'uuid', 'exists:modules,id'],
            'modules.*.is_included' => ['required_with:modules', 'boolean'],
            'modules.*.price_charged' => ['nullable', 'numeric', 'min:0'],

            'add_ons' => ['nullable', 'array'],
            'add_ons.*.add_on_id' => ['required_with:add_ons', 'uuid', 'exists:add_ons,id'],
            'add_ons.*.is_available' => ['required_with:add_ons', 'boolean'],
        ];
    }
}
