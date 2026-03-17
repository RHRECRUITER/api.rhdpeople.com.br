<?php

namespace App\Repositories\Contracts;

interface PasswordRecoveryRepositoryInterface 
{
    public function createToken(string $userId);
    public function findValidToken(string $token);
    public function invalidateToken(string $token); 
}