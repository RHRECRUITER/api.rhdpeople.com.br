<?php
namespace App\Http\Requests\Admin\Module;

use App\Http\Requests\Admin\Module\BaseModuleRequest;

class StoreModuleRequest extends BaseModuleRequest
{
    public function rules(): array
    {
        $rules = $this->commonRules();

        $rules['key'][]   = 'required';
        $rules['label'][] = 'required';

        return $rules;
    }
}
