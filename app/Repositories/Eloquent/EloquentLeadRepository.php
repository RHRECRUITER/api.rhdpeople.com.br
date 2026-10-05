<?php

namespace App\Repositories\Eloquent;

use App\Models\Tracking\Lead;
use App\Repositories\Contracts\LeadRepositoryInterface;

class EloquentLeadRepository implements LeadRepositoryInterface
{
    public function __construct(
        protected Lead $model
    ) {}

    public function updateOrCreateLead(string $sessionId, array $data)
    {
        return $this->model->updateOrCreate(
            ['session_id' => $sessionId],
            [
                'name' => $data['name'] ?? null,
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'last_field_filled' => $data['last_field_filled'] ?? null,
                'status' => $data['status'] ?? 'draft',
                'company_name' => $data['company_name'] ?? null,
                'employee_range'=> $data['employee_range'] ?? "0",
                'challenge' => $data['challenge'] ?? null,
            ]
        );
    }

    public function markAsConverted(string $sessionId, array $finalData = [])
    {
        return $this->model->where('session_id', $sessionId)->update(array_merge($finalData, [
            'status' => 'converted',
            'converted_at' => now(),
        ]));
    }

    public function getConvertedLeads(int $perPage = 15)
    {
        return $this->model->where('status', 'converted')
            ->orderBy('converted_at', 'desc')
            ->paginate($perPage);
    }
}
