<?php

namespace App\Http\Controllers\Api\Audit\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Audit\V1\QueryAuditLogRequest;
use App\Http\Requests\Audit\V1\StoreAuditLogRequest;
use App\Jobs\StoreAuditLogJob;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Response;

class LogsController extends Controller
{
    public function store(StoreAuditLogRequest $request)
    {
        StoreAuditLogJob::dispatch($request->validated())
            ->afterCommit()
            ->onConnection('redis')
            ->onQueue('audit-logs');

        return Response::api([
            'accepted' => true,
        ], 'Log accepted for processing', 202);
    }

    public function index(QueryAuditLogRequest $request)
    {
        $filters = $request->validated();

        $query = AuditLog::query();

        if (! empty($filters['tenant_id'])) {
            $query->where('tenant_id', $filters['tenant_id']);
        }

        if (! empty($filters['actor_id'])) {
            $query->where('actor_id', $filters['actor_id']);
        }

        if (! empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (! empty($filters['timestamp_from'])) {
            $query->where('timestamp', '>=', $filters['timestamp_from']);
        }

        if (! empty($filters['timestamp_to'])) {
            $query->where('timestamp', '<=', $filters['timestamp_to']);
        }

        $logs = $query
            ->orderByDesc('timestamp')
            ->orderByDesc('id')
            ->paginate($filters['per_page'] ?? 25);

        return Response::api([
            'items' => $logs->items(),
            'pagination' => [
                'current_page' => $logs->currentPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
                'last_page' => $logs->lastPage(),
                'from' => $logs->firstItem(),
                'to' => $logs->lastItem(),
            ],
        ]);
    }
}
