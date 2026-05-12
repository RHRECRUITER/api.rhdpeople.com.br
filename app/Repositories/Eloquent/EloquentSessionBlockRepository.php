<?php

namespace App\Repositories\Eloquent;

use App\Models\Tracking\SessionBlock;
use App\Repositories\Contracts\SessionBlockRepositoryInterface;

class EloquentSessionBlockRepository implements SessionBlockRepositoryInterface
{
    public function __construct(
        protected SessionBlock $model
    ) {}

    public function updateOrCreateFromTracking(string $sessionId, array $data)
    {
        return $this->model->updateOrCreate(
            ['session_id' => $sessionId],
            [
                'tenant_id' => $data['tenant_id'],
                'started_at' => $data['started_at'],
                'ended_at' => $data['ended_at'] ?? now(),
                'duration_seconds' => $data['duration_seconds'] ?? 0,
                'landing_page' => $data['landing_page'] ?? null,
                'exit_page' => $data['exit_page'] ?? null,
            ]
        );
    }

    public function markAsConverted(string $sessionId): bool
    {
        return (bool) $this->model
            ->where('session_id', $sessionId)
            ->update(['is_converted' => true]);
    }

    public function findBySessionId(string $sessionId)
    {
        return $this->model->where('session_id', $sessionId)->first();
    }

    public function getRecentUnconverted(string $tenantId, int $limit = 10)
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('is_converted', false)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
