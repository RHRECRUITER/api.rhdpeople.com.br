<?php

namespace Database\Factories;

use App\Enums\SubscriptionInterval;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    protected $model = Plan::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $modules_properties = ['controle_de_ponto', 'ferias_e_licencas', 'folha_de_pagamento', 'beneficios', 'documentos_e_assinaturas', 'relatorios_exite_auditoria'];
        $add_ons_properties = ['KYC_por_batida', 'GPS_obrigatorio', 'Assinatura_digital_juridica', 'exportacao_avancada', 'integracao_contabeis', 'trilha_de_auditoria_avancada', 'suporte_premium_SLA', 'SSO'];

        return [
            'id' => fake()->uuid(),
            'type_plan' => fake()->randomElement(['a', 'b', 'c', 'd', 'e']),
            'number_employees' => fake()->randomNumber(4, false),
            'subscription_interval' => fake()->randomElement(SubscriptionInterval::cases()),
            'price_plan' => fake()->randomFloat(2, 10, 10000),
            'price_subscription' => fake()->randomFloat(2, 100, 20000),
            'modules' => array_combine($modules_properties, array_map(function () {
                return (bool) fake()->boolean(70);
            }, $modules_properties)),
            'add_ons' => array_combine($add_ons_properties, array_map(function () {
                return (bool) fake()->boolean(70);
            }, $add_ons_properties)),
            // 'company_id'=> company::class,
            'contract_id' => Contract::class,
        ];
    }
}
