<?php

namespace Tests\Feature;

use App\Domain\Landing\LeadsStore;
use Tests\TestCase;

class LeadsApiTest extends TestCase
{
 public function test_it_rejects_lead_if_honeypot_is_filled()
    {
        $payload = [
            'name' => 'João Silva',
            'corporate_email' => 'joao@tech.com',
            'objective' => ['payroll' => true],
            'form_started_at' => now()->subMinutes(5)->timestamp,
            'website' => 'http://spam-bot.com', // Honeypot preenchido!
        ];

        $response = $this->postJson('/api/v1/landing/leads', $payload);

        // Assumindo que o Laravel retorna 422 quando a validação falha
        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonStructure(['errors' => ['website']]);
    }

    public function test_it_rejects_lead_if_time_trap_is_too_fast()
    {
        $payload = [
            'name' => 'João Silva',
            'corporate_email' => 'joao@tech.com',
            'objective' => ['payroll' => true],
            // form_started_at no mesmo segundo de agora (preenchimento "imediato" feito por bot)
            'form_started_at' => now()->timestamp,
            'website' => '',
        ];

        $response = $this->postJson('/api/v1/landing/leads', $payload);

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['form_started_at']]);
    }

    private SpyLeadsStore $store;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = new SpyLeadsStore();
        $this->app->instance(LeadsStore::class, $this->store);
    }

    public function test_1_1_valid_submission_is_successful_and_persisted(): void
    {
        $payload = $this->validPayload();

        $response = $this->postJson('/api/v1/landing/leads', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Lead created')
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta' => ['timestamp'],
            ]);

        $this->assertCount(1, $this->store->created);
    }

    public function test_1_2_honeypot_is_silently_blocked_without_persisting(): void
    {
        $payload = $this->validPayload([
            'corporate_email' => 'honeypot-' . uniqid() . '@gmail.com',
            'website' => 'http://spam.com',
        ]);

        $response = $this->postJson('/api/v1/landing/leads', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Lead created')
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta' => ['timestamp'],
            ]);

        $this->assertCount(0, $this->store->created);
    }

    public function test_1_3_time_trap_is_silently_blocked_for_too_fast_and_too_old(): void
    {
        $tooFastPayload = $this->validPayload([
            'corporate_email' => 'fast-' . uniqid() . '@gmail.com',
            'form_started_at' => now()->subSecond()->timestamp,
        ]);

        $tooOldPayload = $this->validPayload([
            'corporate_email' => 'old-' . uniqid() . '@gmail.com',
            'form_started_at' => now()->subHours(3)->timestamp,
        ]);

        $fastResponse = $this->postJson('/api/v1/landing/leads', $tooFastPayload);
        $oldResponse = $this->postJson('/api/v1/landing/leads', $tooOldPayload);

        $fastResponse->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Lead created');

        $oldResponse->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Lead created');

        $this->assertCount(0, $this->store->created);
    }

    public function test_1_4_rate_limiting_returns_429_on_11th_request(): void
    {
        $ip = '10.10.10.10';

        for ($i = 1; $i <= 10; $i++) {
            $response = $this->withServerVariables(['REMOTE_ADDR' => $ip])
                ->postJson('/api/v1/landing/leads', $this->validPayload([
                    'corporate_email' => 'rate-' . $i . '-' . uniqid() . '@gmail.com',
                ]));

            $response->assertStatus(201);
        }

        $response = $this->withServerVariables(['REMOTE_ADDR' => $ip])
            ->postJson('/api/v1/landing/leads', $this->validPayload([
                'corporate_email' => 'rate-11-' . uniqid() . '@gmail.com',
            ]));

        $response->assertStatus(429)
            ->assertJson([
                'success' => false,
                'message' => 'Too many requests',
                'data' => null,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta' => ['timestamp'],
            ]);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_replace_recursive([
            'name' => 'Joao Silva',
            'company_name' => 'ACME LTDA',
            'corporate_email' => 'qa-' . uniqid() . '@gmail.com',
            'phone' => '11999999999',
            'number_employees' => 25,
            'objective' => [
                'time_tracking' => false,
                'vacation' => false,
                'payroll' => true,
                'documents' => false,
                'reports' => false,
            ],
            'form_started_at' => now()->subMinutes(5)->timestamp,
            'website' => '',
        ], $overrides);
    }
}

class SpyLeadsStore implements LeadsStore
{
    /** @var array<int, array<string, mixed>> */
    public array $created = [];

    public function create(array $lead): array
    {
        $this->created[] = $lead;

        return ['id' => count($this->created)];
    }

    public function all(): array
    {
        return $this->created;
    }
}
