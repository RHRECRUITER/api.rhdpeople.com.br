<?php

namespace App\Http\Requests\Admin\Addon;

use app\Http\Requests\Admin\Addon\BaseAddonRequest;

class StoreAddonRequest extends BaseAddonRequest
{
public function rules(): array
    {
        $rules = $this->commonRules();

        $rules['key'][]   = 'required';
        $rules['label'][] = 'required';

        return $rules;
    }
}


