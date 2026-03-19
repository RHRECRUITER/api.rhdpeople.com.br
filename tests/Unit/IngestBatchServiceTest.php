<?php

namespace Tests\Unit;

use App\Infrastructure\Analytics\InMemoryEventStore;
use App\Services\Analytics\V1\IngestBatchService;
use Tests\TestCase;

class IngestBatchServiceTest extends TestCase
{
    public function test_it_ignores_duplicates_in_the_same_batch()
    {
        $store = new InMemoryEventStore();
        $service = new IngestBatchService($store);

        $payload = [
            'client_id' => '123',
            'events' => [
                [
                    'event_id' => 'abc-123',
                    'type' => 'page_view',
                ],
                [
                    'event_id' => 'abc-123', // ID duplicado no mesmo batch!
                    'type' => 'page_view',
                ]
            ]
        ];

        $result = $service->ingest($payload);

        $this->assertEquals(2, $result['accepted']);
        $this->assertEquals(1, $result['inserted']);
        $this->assertEquals(1, $result['duplicates']);
    }

    public function test_it_cleans_up_invalid_props()
    {
        $store = new InMemoryEventStore();
        $service = new IngestBatchService($store);

        $payload = [
            'client_id' => '123',
            'events' => [
                [
                    'event_id' => 'def-456',
                    'type' => 'page_view',
                    'props' => 'invalido' // Props inválido
                ]
            ]
        ];

        $result = $service->ingest($payload);

        // O serviço soma 1 na chave 'invalid' quando o props não é array
        $this->assertEquals(1, $result['invalid']);
        $this->assertEquals(1, $result['inserted']);
    }
}
