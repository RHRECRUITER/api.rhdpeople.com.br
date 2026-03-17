<?php

namespace App\Repositories\Contracts;

interface EmployeeInviteRepositoryInterface 
{
    public function createToken(string $tenant_id, string $email);
    public function findValidToken(string $token);
    public function findTokenByEmail(string $email);
    public function invalidateToken(string $token); 
}