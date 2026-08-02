<?php

namespace App\Repositories\Eloquent\Syslog;

use App\Models\Syslog\TenantAdminLog;
use App\Repositories\Contracts\Syslog\TenantAdminLogRepositoryInterface;

class EloquentTenantAdminLogRepository implements TenantAdminLogRepositoryInterface
{
    public function create(array $data)
    {
        return TenantAdminLog::create($data);
    }

    public function findById(string $id)
    {
        return TenantAdminLog::findOrFail($id);
    }

    public function getByTenant(string $tenantId)
    {
        return TenantAdminLog::query()
            ->where('tenant_id', $tenantId)
            ->latest('created_at')
            ->get();
    }
}
