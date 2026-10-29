<?php

namespace Tests\Unit\Services\Auth;

use App\Models\User;
use App\Services\Auth\AuthenticationService;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class AuthenticationServiceTest extends TestCase
{
    public function test_autentica_usuario_com_credenciais_validas(): void
    {
        Auth::shouldReceive('attempt')
            ->once()
            ->with([
                'email' => 'teste@example.com',
                'password' => 'Senha123!',
            ])
            ->andReturn(true);

        $service = new AuthenticationService();

        $service->authenticate([
            'email' => 'teste@example.com',
            'password' => 'Senha123!',
        ]);

        $this->assertTrue(true);
    }

    public function test_lanca_excecao_quando_as_credenciais_sao_invalidas(): void
    {
        Auth::shouldReceive('attempt')
            ->once()
            ->with([
                'email' => 'teste@example.com',
                'password' => 'senha-errada',
            ])
            ->andReturn(false);

        $service = new AuthenticationService();

        try {
            $service->authenticate([
                'email' => 'teste@example.com',
                'password' => 'senha-errada',
            ]);

            $this->fail('Era esperada uma ValidationException.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey(
                'email',
                $exception->errors()
            );

            $this->assertContains(
                'As credenciais estão incorretas.',
                $exception->errors()['email']
            );
        }
    }

    public function test_nao_autentica_quando_a_senha_esta_incorreta(): void
    {
        \Illuminate\Support\Facades\Auth::shouldReceive('attempt')
            ->once()
            ->with([
                'email' => 'teste@example.com',
                'password' => 'senha-errada',
            ])
            ->andReturn(false);

        $service = new \App\Services\Auth\AuthenticationService();

        try {
            $service->authenticate([
                'email' => 'teste@example.com',
                'password' => 'senha-errada',
            ]);

            $this->fail('O login deveria ter sido rejeitado.');
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->assertArrayHasKey('email', $exception->errors());
        }
    }
}
