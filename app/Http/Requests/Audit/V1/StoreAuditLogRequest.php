<?php

namespace App\Http\Requests\Audit\V1;

use App\Http\Requests\Base\PublicFormRequest;

class StoreAuditLogRequest extends PublicFormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'tenant_id' => is_string($this->input('tenant_id')) ? trim($this->input('tenant_id')) : $this->input('tenant_id'),
            'actor_id' => is_string($this->input('actor_id')) ? trim($this->input('actor_id')) : $this->input('actor_id'),
            'action' => is_string($this->input('action')) ? strtolower(trim($this->input('action'))) : $this->input('action'),
            'timestamp' => is_string($this->input('timestamp')) ? trim($this->input('timestamp')) : $this->input('timestamp'),
            'payload' => is_array($this->input('payload')) ? $this->input('payload') : $this->input('payload', []),
        ]);
    }

    public function rules(): array
    {
        return [
            'tenant_id' => ['required', 'uuid'],
            'actor_id' => ['nullable', 'uuid'],
            'action' => ['required', 'string', 'max:150'],
            'timestamp' => ['required', 'date'],
            'payload' => ['required', 'array'],
        ];
    }
}
