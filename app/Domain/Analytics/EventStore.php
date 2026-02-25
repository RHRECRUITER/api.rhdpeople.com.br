<?php

namespace App\Domain\Analytics;

interface EventStore
{
    /**
     * @param array<int, array<string, mixed>> $events
     * @return array{inserted:int, duplicates:int}
     */

    public function insertIgnoringDuplicates(array $events): array;
}
