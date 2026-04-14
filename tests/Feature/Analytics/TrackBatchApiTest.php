<?php

namespace Tests\Feature\Analytics;

use App\Domain\Analytics\EventStore;
use App\Infrastructure\Analytics\InMemoryEventStore;
use Illuminate\Support\Str;
use Tests\TestCase;

class TrackBatchApiTest extends TestCase
{
    private function validEvent(array $overrides = []): array
    {
        return array_merge([
            'event_id' => (string) Str::uuid(),
            'type' => 'page_view',
            'occurred_at' => now()->toIso8601String(),
            'page' => '/landing',
        ], $overrides);
    }

    private function validPayload(array $events): array
    {
        return [
            'client_id' => (string) Str::uuid(),
            'session_id' => (string) Str::uuid(),
            'events' => $events,
        ];
    }

    public function test_it_accepts_a_hybrid_batch_and_reports_partial_validation_results()
    {
        $events = [];

        for ($index = 0; $index < 8; $index++) {
            $events[] = $this->validEvent([
                'event_id' => (string) Str::uuid(),
                'type' => $index === 0 ? 'scroll_depth' : 'page_view',
                'props' => $index === 0
                    ? ['scroll_bucket' => '75_100']
                    : ['section' => 'home'],
            ]);
        }

        $events[] = $this->validEvent([
            'type' => 'invalid_type',
        ]);

        $events[] = $this->validEvent([
            'props' => [
                'section' => 'home',
                'not_allowed' => 'trash',
            ],
        ]);

        $response = $this->postJson('/api/v1/analytics/track', $this->validPayload($events));

        $response->assertOk()
            ->assertJsonPath('data.accepted', 10)
            ->assertJsonPath('data.inserted', 8)
            ->assertJsonPath('data.invalid', 2)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'trace_id',
                    'accepted',
                    'inserted',
                    'duplicates',
                    'invalid',
                ],
                'meta' => ['timestamp'],
            ]);
    }

    public function test_it_returns_422_for_invalid_scroll_depth_bucket()
    {
        $payload = $this->validPayload([
            $this->validEvent([
                'type' => 'scroll_depth',
                'props' => ['scroll_bucket' => '100_200'],
            ]),
        ]);

        $response = $this->postJson('/api/v1/analytics/track', $payload);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validation failed');
    }

    public function test_it_deduplicates_across_requests_when_the_store_is_shared()
    {
        $sharedStore = new InMemoryEventStore();
        $this->app->instance(EventStore::class, $sharedStore);

        $eventId = (string) Str::uuid();
        $payload = $this->validPayload([
            $this->validEvent([
                'event_id' => $eventId,
                'type' => 'page_view',
                'props' => ['section' => 'home'],
            ]),
        ]);

        $firstResponse = $this->postJson('/api/v1/analytics/track', $payload);
        $secondResponse = $this->postJson('/api/v1/analytics/track', $payload);

        $firstResponse->assertOk()
            ->assertJsonPath('data.accepted', 1)
            ->assertJsonPath('data.inserted', 1)
            ->assertJsonPath('data.duplicates', 0);

        $secondResponse->assertOk()
            ->assertJsonPath('data.accepted', 1)
            ->assertJsonPath('data.inserted', 0)
            ->assertJsonPath('data.duplicates', 1);
    }
}
