<?php

namespace App\Http\Requests\Admin\Addon;

use App\Http\Requests\Base\PublicFormRequest;
use Illuminate\Validation\Rule;

abstract class BaseAddonRequest extends PublicFormRequest
{
    protected function commonRules(): array
    {
        $addonId = $this->route('addon');

        return [
            'key' => [
                'string',
                'max:255',
                Rule::unique('add_ons', 'key')->ignore($addonId),
            ],
            'label' => ['string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
