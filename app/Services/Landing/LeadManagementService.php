<?php

namespace App\Services\Landing;

use App\Exceptions\DuplicateLeadException;
use App\Repositories\Contracts\LeadRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LeadManagementService
{
    public function __construct(
        protected LeadRepositoryInterface $leadRepository
    ) {}

    /**
     * Processa o registro de lead da Landing Page com deduplicação, telemetria e persistência.
     *
     * @param  array<string, mixed>  $payload
     *
     * @throws DuplicateLeadException
     */
    public function registerLead(array $payload, ?string $ip = null): mixed
    {
        $email = (string) ($payload['corporate_email'] ?? $payload['email'] ?? '');
        $key = 'leads:dedupe:email'.sha1($email);

        if (Cache::has($key)) {
            throw new DuplicateLeadException;
        }

        Cache::put($key, true, now()->addHour());

        Log::info('leads.demo_requested', [
            'name' => $payload['name'] ?? null,
            'company_name' => $payload['company_name'] ?? null,
            'corporate_email' => $email,
            'phone' => $payload['phone'] ?? null,
            'modules' => $payload['objective'] ?? null,
            'employee_range' => $payload['employee_range'] ?? null,
            'ip' => $ip,
        ]);

        $sessionId = (string) ($payload['session_id'] ?? session()->getId() ?: Str::uuid()->toString());

        $data = [
            'name' => $payload['name'] ?? null,
            'email' => $email,
            'phone' => $payload['phone'] ?? null,
            'status' => 'converted',
            'last_field_filled' => 'completed',
            'company_name' => $payload['company_name'] ?? null,
            'employee_range' => $payload['employee_range'] ?? (isset($payload['number_employees']) ? (string) $payload['number_employees'] : null),
            'challenge' => $payload['objective'] ?? $payload['challenge'] ?? null,
        ];

        return $this->leadRepository->updateOrCreateLead($sessionId, $data);
    }
}
