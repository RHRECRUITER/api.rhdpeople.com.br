<?php

namespace App\Domain\Landing;

interface LeadsStore
{
    public function create(array $lead): array;

    public function all(): array;
}
