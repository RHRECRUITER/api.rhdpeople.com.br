<?php

namespace App\Repositories\Eloquent\Syslog;

use App\Models\Syslog\LicenseLog;
use App\Repositories\Contracts\Syslog\LicenseLogRepositoryInterface;

class EloquentLicenseLogRepository implements LicenseLogRepositoryInterface
{
    public function create(array $data)
    {
        return LicenseLog::create($data);
    }

    public function findById(string $id)
    {
        return LicenseLog::findOrFail($id);
    }

    public function getByLicense(string $licenseId)
    {
        return LicenseLog::query()
            ->where('auditable_type', \App\Models\License::class)
            ->where('auditable_id', $licenseId)
            ->latest('created_at')
            ->get();
    }
}
