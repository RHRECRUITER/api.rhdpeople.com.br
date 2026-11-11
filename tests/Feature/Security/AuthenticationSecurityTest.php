<?php

namespace Tests\Feature\Security;

use Tests\TestCase;

class AuthenticationSecurityTest extends TestCase
{
    /**
     * Usuários não autenticados não podem acessar o logout.
     */
    public function test_unauthenticated_user_cannot_access_logout(): void
    {
        $response = $this->postJson('/api/v1/auth/logout');

        $response->assertUnauthorized();
    }

    /**
     * A API deve rejeitar login sem credenciais.
     */
    public function test_login_rejects_missing_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', []);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors([
            'email',
            'password',
        ]);
    }

    /**
     * Credenciais inválidas não devem autenticar o usuário.
     */
    public function test_login_rejects_invalid_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'usuario-inexistente@example.com',
            'password' => 'SenhaIncorreta123!',
        ]);

        $response->assertUnprocessable();

        $this->assertGuest();
    }
}