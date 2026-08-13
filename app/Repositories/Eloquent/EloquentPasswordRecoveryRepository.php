<?php

namespace App\Repositories\Eloquent;

use App\Models\PasswordRecoveryToken;
use App\Repositories\Contracts\PasswordRecoveryRepositoryInterface;
use Carbon\CarbonInterface;

class EloquentPasswordRecoveryRepository implements PasswordRecoveryRepositoryInterface
{
    public function __construct(
        protected PasswordRecoveryToken $model
    ) {}

    public function createToken(string $userId, string $token, CarbonInterface $expiresAt): PasswordRecoveryToken
    {
        return $this->model->create([
            'user_id' => $userId,
            'token' => $token,
            'expires_at' => $expiresAt,
            'created_at' => now(),
        ]);
    }

    public function findByToken(string $token): ?PasswordRecoveryToken
    {
        return $this->model->where('token', $token)->first();
    }

    public function markAsUsed(PasswordRecoveryToken $token): PasswordRecoveryToken
    {
        $token->update([
            'used_at' => now(),
        ]);

        return $token;
    }
}
