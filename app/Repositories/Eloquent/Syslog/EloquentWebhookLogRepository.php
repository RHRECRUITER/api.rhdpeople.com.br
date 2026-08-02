<?php

namespace App\Repositories\Eloquent;

use App\Models\Syslog\WebhookLog;
use App\Repositories\Contracts\Syslog\WebhookLogRepositoryInterface;

class EloquentWebhookLogRepository implements WebhookLogRepositoryInterface
{
    public function create(array $data)
    {
        return WebhookLog::create($data);
    }

    public function findById(string $id)
    {
        return WebhookLog::findOrFail($id);
    }

    public function findByGatewayEventId(string $gatewayEventId)
    {
        return WebhookLog::query()
            ->where('payload->gateway_event_id', $gatewayEventId)
            ->first();
    }

    public function getByCorrelationId(string $correlationId)
    {
        return WebhookLog::query()
            ->where('correlation_id', $correlationId)
            ->oldest('created_at')
            ->get();
    }
}
