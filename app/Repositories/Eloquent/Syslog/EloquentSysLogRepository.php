<?php

namespace App\Repositories\Eloquent\Syslog;

use App\Models\Syslog\SysLog;
use App\Repositories\Contracts\Syslog\SysLogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentSysLogRepository implements SysLogRepositoryInterface
{
    public function create(array $data)
    {
        return SysLog::create($data);
    }

    public function findById(string $id)
    {
        return SysLog::findOrFail($id);
    }

    public function paginateByTenant(?string $tenantId, int $perPage = 25): LengthAwarePaginator
    {
        return SysLog::query()
            ->when($tenantId, fn ($query) => $query->where('tenant_id', $tenantId))
            ->latest('created_at')
            ->paginate($perPage);
    }

    public function getByType(string $type, ?string $tenantId = null)
    {
        return SysLog::query()
            ->where('type', $type)
            ->when($tenantId, fn ($query) => $query->where('tenant_id', $tenantId))
            ->latest('created_at')
            ->get();
    }

    public function getByCorrelationId(string $correlationId)
    {
        return SysLog::query()
            ->where('correlation_id', $correlationId)
            ->oldest('created_at')
            ->get();
    }

    public function getByAuditable(string $auditableType, string $auditableId)
    {
        return SysLog::query()
            ->where('auditable_type', $auditableType)
            ->where('auditable_id', $auditableId)
            ->latest('created_at')
            ->get();
    }

    public function getBySeverity(string $severity, ?string $tenantId = null)
    {
        return SysLog::query()
            ->where('severity', $severity)
            ->when($tenantId, fn ($query) => $query->where('tenant_id', $tenantId))
            ->latest('created_at')
            ->get();
    }

    public function getByUser(string $userId)
    {
        return SysLog::query()
            ->where('user_id', $userId)
            ->latest('created_at')
            ->get();
    }
}
