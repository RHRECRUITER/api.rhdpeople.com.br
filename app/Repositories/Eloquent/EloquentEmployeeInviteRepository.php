<?php

namespace App\Repositories\Eloquent;

use App\Models\EmployeeInviteToken;
use App\Repositories\Contracts\EmployeeInviteRepositoryInterface;
use Illuminate\Support\Str;

class EloquentEmployeeInviteRepository implements EmployeeInviteRepositoryInterface
{
    public function __construct(
        protected EmployeeInviteToken $model
    ) {}

    public function createToken(string $tenant_id, string $email)
    {
        $newToken = Str::random(64);
        $expiresAt = now()->addHours(72);

        return $this->model->create([
            'tenant_id' => $tenant_id,
            'email' => $email,
            'token' => $newToken,
            'expires_at' => $expiresAt,
            'created_at' => now(),
        ]);
    }

    public function findValidToken(string $token)
    {
        return $this->model->where('token', $token)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();
    }

    public function findTokenByEmail(string $email)
    {
        return $this->model->where('email', $email)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();
    }

    public function invalidateToken(string $token)
    {
        $tokenToInvalidate = $this->findValidToken($token);
        if ($tokenToInvalidate) {
            $tokenToInvalidate->update([
                'used_at' => now(),
            ]);
        }

        return $tokenToInvalidate;
    }
}
