<?php

namespace App\Infrastructure\Landing;

use App\Domain\Landing\LeadsStore;

class InMemoryLeadsStore implements LeadsStore
{
    /** @var array<int, array<string, mixed>> */
    private array $items = [];

    private int $nextId = 1;

    public function create(array $lead): array
    {
        $id = $this->nextId++;

        $this->items[$id] = $lead + [
            'id' => $id,
            'created_at' => now()->toIso8601String(),
        ];

        return ['id' => $id];
    }

    public function all(): array
    {
        return array_values($this->items);
    }
}
