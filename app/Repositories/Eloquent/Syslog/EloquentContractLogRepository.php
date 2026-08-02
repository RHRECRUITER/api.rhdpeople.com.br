<?php

namespace App\Repositories\Eloquent\Syslog;

use App\Models\Syslog\ContractLog;
use App\Repositories\Contracts\Syslog\ContractLogRepositoryInterface;

class EloquentContractLogRepository implements ContractLogRepositoryInterface
{
    public function create(array $data)
    {
        return ContractLog::create($data);
    }

    public function findById(string $id)
    {
        return ContractLog::findOrFail($id);
    }

    public function getByContract(string $contractId)
    {
        return ContractLog::query()
            ->where('auditable_type', \App\Models\Contract::class)
            ->where('auditable_id', $contractId)
            ->latest('created_at')
            ->get();
    }

    public function lastEventForContract(string $contractId)
    {
        return ContractLog::query()
            ->where('auditable_type', \App\Models\Contract::class)
            ->where('auditable_id', $contractId)
            ->latest('created_at')
            ->first();
    }
}
