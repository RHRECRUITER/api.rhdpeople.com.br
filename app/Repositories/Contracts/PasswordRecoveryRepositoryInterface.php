<?php

namespace App\Repositories\Contracts;

use App\Models\PasswordRecoveryToken;
use Carbon\CarbonInterface;

interface PasswordRecoveryRepositoryInterface
{
    public function createToken(string $userId, string $token, CarbonInterface $expiresAt): PasswordRecoveryToken;

    public function findByToken(string $token): ?PasswordRecoveryToken;

    public function markAsUsed(PasswordRecoveryToken $token): PasswordRecoveryToken;
}
