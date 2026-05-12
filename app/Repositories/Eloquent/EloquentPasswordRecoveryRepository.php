<?php

namespace App\Repositories\Eloquent;

use App\Models\PasswordRecoveryToken;
use App\Repositories\Contracts\PasswordRecoveryRepositoryInterface;
use Illuminate\Support\Str;

class EloquentPasswordRecoveryRepository implements PasswordRecoveryRepositoryInterface
{
    public function __construct(
        protected PasswordRecoveryToken $model
    ) {}

    public function createToken(string $userId)
    {
        $newToken = Str::random(64);
        $expiresAt = now()->addHours(72);

        return $this->model->create([
            'user_id' => $userId,
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
