<?php

namespace App\DTOs\Auth;

readonly class RegisterOwnerDTO
{
    public function __construct(
        public array $userData,
        public array $companyData,
        public array $planData
    ) {}
}
