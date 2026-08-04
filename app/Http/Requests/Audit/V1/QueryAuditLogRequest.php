<?php

namespace App\Http\Requests\Audit\V1;

use App\Http\Requests\Base\PublicFormRequest;

class QueryAuditLogRequest extends PublicFormRequest
{
    public function rules(): array
    {
        return [
            'tenant_id' => ['sometimes', 'uuid'],
            'actor_id' => ['sometimes', 'uuid'],
            'action' => ['sometimes', 'string', 'max:150'],
            'timestamp_from' => ['sometimes', 'date'],
            'timestamp_to' => ['sometimes', 'date', 'after_or_equal:timestamp_from'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}
