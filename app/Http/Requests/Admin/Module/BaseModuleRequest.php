<?php
namespace App\Http\Requests\Admin\Module;

use App\Http\Requests\Base\PublicFormRequest;
use Illuminate\Validation\Rule;

abstract class BaseModuleRequest extends PublicFormRequest
{
    protected function commonRules(): array
    {
        $moduleId = $this->route('module');
        return [
            'key'         => [
                'string',
                'max:255',
                Rule::unique('modules', 'key')->ignore($moduleId),
            ],
            'label'       => ['string', 'max:255'],
            'description' => ['nullable', 'string'],
            'extra_price' => ['nullable', 'numeric', 'min:0'],
            'is_active'   => ['boolean'],
        ];
    }
}
