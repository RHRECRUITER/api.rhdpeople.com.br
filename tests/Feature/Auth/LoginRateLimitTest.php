<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Class LoginRateLimitTest
 *
 * Cobre a política de proteção contra força bruta no login:
 * - 5 falhas por e-mail em 15 minutos.
 * - Bloqueio (429) persiste mesmo quando as tentativas vêm de IPs diferentes.
 * - Resposta idêntica para conta existente e inexistente (anti-enumeração).
 * - Sucesso no login limpa o contador de falhas.
 */
class LoginRateLimitTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();

        $authMock = Auth::partialMock();
        $authMock->shouldReceive('userResolver')->byDefault()->andReturn(fn () => null);
    }

    public function test_it_blocks_after_five_failed_attempts_on_same_email(): void
    {
        Auth::partialMock()
            ->shouldReceive('attempt')
            ->times(5)
            ->andReturn(false);

        $payload = ['email' => 'vitima@example.com', 'password' => 'senha-errada'];

        for ($i = 1; $i <= 5; $i++) {
            $this->postJson('/api/v1/auth/login', $payload)
                ->assertStatus(422);
        }

        // 6ª tentativa: deve ser bloqueada com status 429
        $this->postJson('/api/v1/auth/login', $payload)
            ->assertStatus(429)
            ->assertJsonPath('success', false);
    }

    public function test_it_persists_block_even_when_request_comes_from_different_ip(): void
    {
        Auth::partialMock()
            ->shouldReceive('attempt')
            ->times(5)
            ->andReturn(false);

        $payload = ['email' => 'vitima@example.com', 'password' => 'senha-errada'];

        // 5 falhas a partir do IP A
        for ($i = 1; $i <= 5; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])
                ->postJson('/api/v1/auth/login', $payload)
                ->assertStatus(422);
        }

        // 6ª tentativa a partir do IP B: ainda bloqueada (contador atrelado ao e-mail)
        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.2'])
            ->postJson('/api/v1/auth/login', $payload)
            ->assertStatus(429)
            ->assertJsonPath('success', false);
    }

    public function test_it_returns_identical_response_for_existing_and_non_existing_accounts(): void
    {
        Auth::partialMock()
            ->shouldReceive('attempt')
            ->twice()
            ->andReturn(false);

        $existing = $this->postJson('/api/v1/auth/login', [
            'email' => 'existe@example.com',
            'password' => 'senha-errada',
        ]);

        $nonExisting = $this->postJson('/api/v1/auth/login', [
            'email' => 'nao-existe@example.com',
            'password' => 'senha-errada',
        ]);

        $this->assertSame(422, $existing->status());
        $this->assertSame(422, $nonExisting->status());
        $this->assertSame(
            $existing->json('errors.email'),
            $nonExisting->json('errors.email')
        );
    }

    public function test_it_clears_rate_limit_attempts_on_successful_login(): void
    {
        $email = 'ok@example.com';

        $mockUser = \Mockery::mock(User::class)->makePartial();
        $mockUser->shouldReceive('load')->andReturnSelf();
        $mockUser->shouldReceive('isSuperAdmin')->andReturn(false);
        $mockUser->shouldReceive('isCompanyAdmin')->andReturn(false);
        $mockUser->shouldReceive('licenses->where->exists')->andReturn(false);
        $mockUser->shouldReceive('licenses->with->where->first')->andReturn(null);
        $mockUser->id = 1;
        $mockUser->name = 'Usuário Teste';
        $mockUser->email = $email;

        $authMock = Auth::partialMock();

        // Simula 3 falhas iniciais
        $authMock->shouldReceive('attempt')
            ->times(3)
            ->andReturn(false);

        for ($i = 1; $i <= 3; $i++) {
            $this->postJson('/api/v1/auth/login', [
                'email' => $email,
                'password' => 'senha-errada',
            ])->assertStatus(422);
        }

        $authMock->shouldReceive('attempt')
            ->once()
            ->andReturn(true);
        $authMock->shouldReceive('user')
            ->andReturn($mockUser);

        // Login com sucesso
        $this->postJson('/api/v1/auth/login', [
            'email' => $email,
            'password' => 'senha-correta',
        ])->assertStatus(200);

        // Contador resetado: 5 novas falhas são permitidas antes do bloqueio
        $authMock->shouldReceive('attempt')
            ->times(5)
            ->andReturn(false);

        for ($i = 1; $i <= 5; $i++) {
            $this->postJson('/api/v1/auth/login', [
                'email' => $email,
                'password' => 'senha-errada',
            ])->assertStatus(422);
        }

        // A 6ª nova falha deve bloquear com status 429
        $this->postJson('/api/v1/auth/login', [
            'email' => $email,
            'password' => 'senha-errada',
        ])->assertStatus(429);
    }
}
