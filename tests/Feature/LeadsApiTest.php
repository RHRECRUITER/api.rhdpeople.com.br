<?php

namespace Tests\Feature;

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
}
