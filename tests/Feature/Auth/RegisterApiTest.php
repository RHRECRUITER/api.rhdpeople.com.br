<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class RegisterApiTest extends TestCase
{
    public function test_cadastro_retorna_erros_quando_os_campos_obrigatorios_faltam(): void
    {
        $response = $this->postJson('/api/v1/auth/register', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
                'email',
                'password',
                'company.name',
                'company.cnpj',
                'company.email',
                'company.phone',
                'plan.plan_tier_id',
                'plan.employee_range_id',
                'plan.price_total',
            ]);
    }

    public function test_cadastro_retorna_erro_quando_email_e_invalido(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Lucas',
            'email' => 'email-invalido',
            'password' => 'Senha123!',
            'company' => [
                'name' => 'Empresa Teste',
                'cnpj' => '12345678000199',
                'email' => 'empresa@example.com',
                'phone' => '83999999999',
            ],
            'plan' => [
                'plan_tier_id' => '123e4567-e89b-12d3-a456-426614174000',
                'employee_range_id' => '123e4567-e89b-12d3-a456-426614174001',
                'price_total' => 199.90,
            ],
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_cadastro_retorna_erro_quando_senha_tem_menos_de_oito_caracteres(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Lucas',
            'email' => 'lucas@example.com',
            'password' => '123',
            'company' => [
                'name' => 'Empresa Teste',
                'cnpj' => '12345678000199',
                'email' => 'empresa@example.com',
                'phone' => '83999999999',
            ],
            'plan' => [
                'plan_tier_id' => '123e4567-e89b-12d3-a456-426614174000',
                'employee_range_id' => '123e4567-e89b-12d3-a456-426614174001',
                'price_total' => 199.90,
            ],
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    }


    public function test_cadastro_exige_nome_da_empresa(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Lucas',
            'email' => 'lucas@example.com',
            'password' => 'Senha123!',
            'company' => [
                'cnpj' => '12345678000199',
                'email' => 'empresa@example.com',
                'phone' => '83999999999',
            ],
            'plan' => [
                'plan_tier_id' => '123e4567-e89b-12d3-a456-426614174000',
                'employee_range_id' => '123e4567-e89b-12d3-a456-426614174001',
                'price_total' => 199.90,
            ],
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['company.name']);
    }

}
