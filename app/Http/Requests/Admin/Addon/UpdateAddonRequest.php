<?php

namespace App\Http\Requests\Admin\Addon;

class UpdateAddonRequest extends BaseAddonRequest
{
    public function rules(): array
    {
        $rules = $this->commonRules();

        return array_map(function ($rule) {
            array_unshift($rule, 'sometimes');

            return $rule;
        }, $rules);
    }
}
