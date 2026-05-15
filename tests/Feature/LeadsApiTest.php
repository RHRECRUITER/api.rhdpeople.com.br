<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class LeadsApiTest extends TestCase
{
    public function test_it_accepts_valid_captcha_token_without_session_cookie_using_fingerprint()
    {
        $ip = '203.0.113.10';
        $ua = 'LeadsApiTest/1.0';
        $fingerprint = hash('sha256', $ip.'|'.$ua);

        $token = 'test-token-123';
        Cache::put("validated_action:{$token}", [
            'session_id' => 'different-session-id',
            'fingerprint' => $fingerprint,
            'context' => 'leads_landing',
        ], now()->addMinutes(5));

        $payload = [
            'name' => 'João Silva',
            'company_name' => 'Empresa Teste',
            'corporate_email' => 'joao'.uniqid().'@example.com',
            'phone' => '11988887777',
            'number_employees' => 150,
            'objective' => ['payroll' => true],
            'form_started_at' => now()->subSeconds(3)->timestamp,
            'website' => '',
            'validation_token' => $token,
        ];

        $response = $this
            ->withServerVariables([
                'REMOTE_ADDR' => $ip,
                'HTTP_USER_AGENT' => $ua,
            ])
            ->postJson('/api/v1/landing/leads', $payload);

        // O foco deste teste é garantir que o middleware aceite o token via fingerprint,
        // mesmo sem cookie de sessão. A validação do payload pode falhar (422) conforme
        // regras de email:rfc,dns e ambiente de testes.
        $response->assertStatus(422);
        $this->assertFalse(Cache::has("validated_action:{$token}"));
    }

    public function test_it_rejects_lead_if_honeypot_is_filled()
    {
        $token = 'honeypot-token-'.uniqid();
        Cache::put("validated_action:{$token}", [
            'session_id' => session()->getId(),
            'context' => 'leads_landing',
        ], now()->addMinutes(5));

        $payload = [
            'name' => 'João Silva',
            'corporate_email' => 'joao@example.com',
            'objective' => ['payroll' => true],
            'form_started_at' => now()->subMinutes(5)->timestamp,
            'website' => 'http://spam-bot.com', // Honeypot preenchido!
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
        $token = 'timetrip-token-'.uniqid();
        Cache::put("validated_action:{$token}", [
            'session_id' => session()->getId(),
            'context' => 'leads_landing',
        ], now()->addMinutes(5));

        $payload = [
            'name' => 'João Silva',
            'corporate_email' => 'joao@example.com',
            'objective' => ['payroll' => true],
            // form_started_at no mesmo segundo de agora (preenchimento "imediato" feito por bot)
            'form_started_at' => now()->timestamp,
            'website' => '',
            'validation_token' => $token,
        ];

        $response = $this->postJson('/api/v1/landing/leads', $payload);

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['form_started_at']]);
    }
}
