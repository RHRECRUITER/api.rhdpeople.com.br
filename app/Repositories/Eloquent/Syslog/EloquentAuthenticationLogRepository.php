<?php

namespace App\Repositories\Eloquent\Syslog;

use App\Models\Syslog\AuthenticationLog;
use App\Repositories\Contracts\Syslog\AuthenticationLogRepositoryInterface;
use Carbon\Carbon;

class EloquentAuthenticationLogRepository implements AuthenticationLogRepositoryInterface
{
    public function create(array $data)
    {
        return AuthenticationLog::create($data);
    }

    public function findById(string $id)
    {
        return AuthenticationLog::findOrFail($id);
    }

    public function countFailedAttemptsSince(string $userId, Carbon $since): int
    {
        return AuthenticationLog::query()
            ->where('user_id', $userId)
            ->where('action', 'failed_attempt')
            ->where('created_at', '>=', $since)
            ->count();
    }

    public function lastSuccessfulLogin(string $userId)
    {
        return AuthenticationLog::query()
            ->where('user_id', $userId)
            ->where('action', 'login')
            ->latest('created_at')
            ->first();
    }
}
