<?php

namespace App\Http\Requests\Admin\Module;

class UpdateModuleRequest extends BaseModuleRequest
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
