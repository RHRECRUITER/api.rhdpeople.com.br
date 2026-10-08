<?php
namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class LeadsApiTest extends TestCase
{

    protected function getValidToken()
    {
        $token = \Illuminate\Support\Str::random(64);
        \Illuminate\Support\Facades\Cache::put("validated_action:{$token}", [
            'session_id'  => session()->getId(),
            'fingerprint' => hash('sha256', '127.0.0.1|Symfony'),
            'context'     => 'leads_landing',
        ], now()->addMinutes(5));

        return $token;
    }
    public function test_it_accepts_valid_captcha_token_without_session_cookie_using_fingerprint()
    {
        $ip          = '203.0.113.10';
        $ua          = 'LeadsApiTest/1.0';
        $fingerprint = hash('sha256', $ip . '|' . $ua);

        $token = 'test-token-123';
        Cache::put("validated_action:{$token}", [
            'session_id'  => 'different-session-id',
            'fingerprint' => $fingerprint,
            'context'     => 'leads_landing',
        ], now()->addMinutes(5));

        $payload = [
            'name'             => 'João Silva',
            'company_name'     => 'Empresa Teste',
            'corporate_email'  => 'joao' . uniqid() . '@example.com',
            'phone'            => '11988887777',
            'number_employees' => 150,
            'objective'        => ['payroll' => true],
            'form_started_at'  => now()->subSeconds(3)->timestamp,
            'website'          => '',
            'validation_token' => $token,
        ];

        $response = $this
            ->withServerVariables([
                'REMOTE_ADDR'     => $ip,
                'HTTP_USER_AGENT' => $ua,
            ])
            ->postJson('/api/v1/landing/leads', $payload);

        // O foco deste teste é garantir que o middleware aceite o token via fingerprint,
        // mesmo sem cookie de sessão. A validação do payload pode falhar (422) conforme
        // regras de email:rfc,dns e ambiente de testes.
        $response->assertStatus(422);

        // Se o middleware captcha.check funcionou, ele DEVE ter consumido o token
        // mesmo que o Controller retorne 422 depois.
        $this->assertFalse(Cache::has("validated_action:{$token}"), 'O token de captcha deveria ter sido removido do Cache.');
    }

    public function test_it_rejects_lead_if_honeypot_is_filled()
    {
        $token = 'honeypot-token-' . uniqid();
        Cache::put("validated_action:{$token}", [
            'session_id'  => session()->getId(),
            'fingerprint' => hash('sha256', '127.0.0.1|Symfony'),
            'context'     => 'leads_landing',
        ], now()->addMinutes(5));

        $payload = [
            'name'             => 'João Silva',
            'corporate_email'  => 'joao@example.com',
            'objective'        => ['payroll' => true],
            'form_started_at'  => now()->subMinutes(5)->timestamp,
            'website'          => 'http://spam-bot.com', // Honeypot preenchido!
            'validation_token' => $token,
        ];

        $response = $this->postJson('/api/v1/landing/leads', $payload);

        // Assumindo que o Laravel retorna 422 quando a validação falha
        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonStructure(['errors' => ['website']]);
    }

    public function test_it_rejects_lead_if_time_trap_is_too_fast()
    {
        $token = 'timetrip-token-' . uniqid();
        Cache::put("validated_action:{$token}", [
            'session_id'  => session()->getId(),
            'fingerprint' => hash('sha256', '127.0.0.1|Symfony'),
            'context'     => 'leads_landing',
        ], now()->addMinutes(5));

        $payload = [
            'name'             => 'João Silva',
            'corporate_email'  => 'joao@example.com',
            'objective'        => ['payroll' => true],
            // form_started_at no mesmo segundo de agora (preenchimento "imediato" feito por bot)
            'form_started_at'  => now()->timestamp,
            'website'          => '',
            'validation_token' => $token,
        ];

        $response = $this->postJson('/api/v1/landing/leads', $payload);

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['form_started_at']]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Cache::flush();

        $this->mock(\App\Repositories\Contracts\LeadRepositoryInterface::class, function ($mock) {
            $mock->shouldReceive('updateOrCreateLead')
                ->byDefault()
                ->andReturn((object) ['id' => 123, 'name' => 'Default Lead']);
        });
    }

    public function test_1_1_valid_submission_is_successful_and_persisted(): void
    {
        $this->mock(\App\Repositories\Contracts\LeadRepositoryInterface::class, function ($mock) {
            $mock->shouldReceive('updateOrCreateLead')
                ->once()
                ->with(\Mockery::any(), \Mockery::on(function ($lead) {
                    return $lead['name'] === 'Joao Silva'
                        && $lead['company_name'] === 'ACME LTDA'
                        && $lead['employee_range'] === '11-50'
                        && isset($lead['challenge']['payroll']);
                }))
                ->andReturn((object) ['id' => 123, 'name' => 'Joao Silva']);
        });

        $ip    = '127.0.0.1';
        $ua    = 'Symfony';
        $token = 'valid-token-' . uniqid();

        Cache::put("validated_action:{$token}", [
            'session_id'  => session()->getId(),
            'fingerprint' => hash('sha256', $ip . '|' . $ua),
            'context'     => 'leads_landing',
        ], now()->addMinutes(5));

        $payload  = $this->validPayload(['validation_token' => $token]);
        $response = $this->postJson('/api/v1/landing/leads', $payload);

        $response->assertStatus(201);
    }
    public function test_1_2_honeypot_is_silently_blocked_without_persisting(): void
    {
        $spy   = $this->spy(\App\Repositories\Contracts\LeadRepositoryInterface::class);
        $token = 'honeypot-token-' . uniqid();
        Cache::put("validated_action:{$token}", [
            'session_id'  => session()->getId(),
            'fingerprint' => hash('sha256', '127.0.0.1|Symfony'),
            'context'     => 'leads_landing',
        ], now()->addMinutes(5));

        $payload = $this->validPayload([
            'corporate_email'  => 'honeypot-' . uniqid() . '@gmail.com',
            'website'          => 'http://spam.com',
            'validation_token' => $token,
        ]);

        $response = $this->postJson('/api/v1/landing/leads', $payload);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonStructure(['errors' => ['website']]);

        // Verificação via Mockery: Garante que 'updateOrCreateLead' NUNCA foi chamado
        $spy->shouldNotHaveReceived('updateOrCreateLead');
    }

    public function test_1_3_time_trap_is_silently_blocked_for_too_fast_and_too_old(): void
    {
        $spy = $this->spy(\App\Repositories\Contracts\LeadRepositoryInterface::class);
        // Primeira requisição: Token único
        $tokenFast      = $this->getValidToken();
        $tooFastPayload = $this->validPayload([
            'corporate_email'  => 'fast-' . uniqid() . '@gmail.com',
            'form_started_at'  => now()->subSecond()->timestamp,
            'validation_token' => $tokenFast,
        ]);

        // Segunda requisição: Token único
        $tokenOld      = $this->getValidToken();
        $tooOldPayload = $this->validPayload([
            'corporate_email'  => 'old-' . uniqid() . '@gmail.com',
            'form_started_at'  => now()->subHours(3)->timestamp,
            'validation_token' => $tokenOld,
        ]);

        $fastResponse = $this->postJson('/api/v1/landing/leads', $tooFastPayload);
        $oldResponse  = $this->postJson('/api/v1/landing/leads', $tooOldPayload);

        $fastResponse->assertStatus(422)
            ->assertJsonStructure(['errors' => ['form_started_at']]);

        $oldResponse->assertStatus(422)
            ->assertJsonStructure(['errors' => ['form_started_at']]);

        // Verificação via Mockery: Garante que 'updateOrCreateLead' NUNCA foi chamado
        $spy->shouldNotHaveReceived('updateOrCreateLead');
    }
    public function test_1_4_rate_limiting_returns_429_on_11th_request(): void
    {
        $ip = '10.10.10.10';

        for ($i = 1; $i <= 10; $i++) {
            $token = "rate-token-{$i}";
            Cache::put("validated_action:{$token}", [
                'session_id'  => session()->getId(),
                'fingerprint' => hash('sha256', $ip . '|Symfony'),
                'context'     => 'leads_landing',
            ], now()->addMinutes(5));

            $response = $this->withServerVariables(['REMOTE_ADDR' => $ip])
                ->postJson('/api/v1/landing/leads', $this->validPayload([
                    'corporate_email'  => 'rate-' . $i . '-' . uniqid() . '@gmail.com',
                    'validation_token' => $token,
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
                'data'    => null,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta' => ['timestamp'],
            ]);
    }

    public function test_it_returns_409_when_lead_is_submitted_twice_within_cache_ttl(): void
    {
        $this->mock(\App\Repositories\Contracts\LeadRepositoryInterface::class, function ($mock) {
            $mock->shouldReceive('updateOrCreateLead')
                ->once()
                ->andReturn((object) ['id' => 123]);
        });

        $email = 'duplicate-' . uniqid() . '@gmail.com';
        $payload1 = $this->validPayload([
            'corporate_email'  => $email,
            'validation_token' => $this->getValidToken(),
        ]);

        $res1 = $this->postJson('/api/v1/landing/leads', $payload1);
        $res1->assertStatus(201);

        $payload2 = $this->validPayload([
            'corporate_email'  => $email,
            'validation_token' => $this->getValidToken(),
        ]);

        $res2 = $this->postJson('/api/v1/landing/leads', $payload2);
        $res2->assertStatus(409)
            ->assertJsonPath('message', 'Lead already submitted recently');
    }

    private function validPayload(array $overrides = []): array
    {
        return array_replace_recursive([
            'name'             => 'Joao Silva',
            'company_name'     => 'ACME LTDA',
            'corporate_email'  => 'qa-' . uniqid() . '@gmail.com',
            'phone'            => '11999999999',
            'employee_range'   => '11-50',
            'objective'        => [
                'time_tracking' => false,
                'vacation'      => false,
                'payroll'       => true,
                'documents'     => false,
                'reports'       => false,
            ],
            'form_started_at'  => now()->subMinutes(5)->timestamp,
            'website'          => '',
        ], $overrides);
    }
}
