<?php

namespace Tests\Feature\Security;

use Tests\TestCase;

class InputValidationSecurityTest extends TestCase
{
    /**
     * O cadastro deve rejeitar campos obrigatórios ausentes.
     */
    public function test_register_rejects_missing_required_fields(): void
    {
        $response = $this->postJson('/api/v1/auth/register', []);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors([
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

    /**
     * A API deve rejeitar formatos inválidos.
     */
    public function test_register_rejects_invalid_input_formats(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Usuário Teste',
            'email' => 'email-invalido',
            'password' => '123',

            'company' => [
                'name' => 'Empresa Teste',
                'cnpj' => '12345678000199',
                'email' => 'email-invalido',
                'phone' => '83999999999',
            ],

            'plan' => [
                'plan_tier_id' => 'uuid-invalido',
                'employee_range_id' => 'uuid-invalido',
                'price_total' => 'valor-invalido',
            ],
        ]);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors([
            'email',
            'password',
            'company.email',
            'plan.plan_tier_id',
            'plan.employee_range_id',
            'plan.price_total',
        ]);
    }
}