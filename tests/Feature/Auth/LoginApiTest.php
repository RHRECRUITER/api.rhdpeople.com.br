<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class LoginApiTest extends TestCase
{
    public function test_login_retorna_erro_quando_email_e_senha_nao_sao_enviados(): void
    {
        $response = $this->postJson('/api/v1/auth/login', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_retorna_erro_quando_email_e_invalido(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'email-invalido',
            'password' => 'Senha123!',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_retorna_erro_quando_senha_nao_e_enviada(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'lucas@example.com',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    }


    public function test_logout_invalida_a_sessao(): void
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user, 'web')
            ->withSession(['teste' => 'valor'])
            ->postJson('/api/v1/auth/logout');

        $response->dump();

        $response->assertOk();
    }
}
