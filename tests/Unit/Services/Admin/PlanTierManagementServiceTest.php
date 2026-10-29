<?php

namespace Tests\Unit\Services\Admin;

use App\DTOs\Admin\PlanTierDTO;
use App\Models\AddOn;
use App\Models\Module;
use App\Models\PlanTier;
use App\Services\Admin\PlanTierManagementService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PlanTierManagementServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected $connectionsToTransact = ['conn_rhdpeople_geral'];

    private PlanTierManagementService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new PlanTierManagementService();
    }

    public function test_it_creates_a_plan_tier(): void
    {
        $dto = new PlanTierDTO(
            name: 'Plano Básico',
            basePrice: 99.90,
            maxEmployees: 10,
            annualDiscountPct: 10.0,
            isActive: true,
            modules: [],
            addOns: [],
        );

        $plan = $this->service->createPlanTier($dto);

        $this->assertInstanceOf(PlanTier::class, $plan);

        $this->assertDatabaseHas('plan_tiers', [
            'id' => $plan->id,
            'name' => 'Plano Básico',
            'base_price' => 99.90,
            'max_employees' => 10,
            'is_active' => true,
        ], 'conn_rhdpeople_geral');
    }

    public function test_it_updates_basic_plan_tier_fields(): void
    {
        $plan = $this->service->createPlanTier(
            new PlanTierDTO(
                name: 'Plano Antigo',
                basePrice: 50.00,
                maxEmployees: 5,
                annualDiscountPct: 0.0,
                isActive: true,
                modules: [],
                addOns: [],
            )
        );

        $updated = $this->service->updatePlanTier(
            (string) $plan->id,
            new PlanTierDTO(
                name: 'Plano Atualizado',
                basePrice: 79.90,
                maxEmployees: null,
                annualDiscountPct: null,
                isActive: false,
                modules: null,
                addOns: null,
            )
        );

        $this->assertSame($plan->id, $updated->id);

        $this->assertDatabaseHas('plan_tiers', [
            'id' => $plan->id,
            'name' => 'Plano Atualizado',
            'base_price' => 79.90,
            'max_employees' => 5,
            'is_active' => false,
        ], 'conn_rhdpeople_geral');
    }

    public function test_it_creates_a_plan_with_modules_and_addons(): void
    {
        $module = Module::create([
            'key' => 'reports',
            'label' => 'Relatórios',
            'description' => 'Relatórios do sistema',
            'extra_price' => 0,
            'is_active' => true,
        ]);

        $addon = AddOn::create([
            'key' => 'extra_storage',
            'label' => 'Armazenamento extra',
            'description' => 'Espaço adicional',
            'price' => 20,
            'is_active' => true,
        ]);

        $plan = $this->service->createPlanTier(
            new PlanTierDTO(
                name: 'Plano Profissional',
                basePrice: 199.90,
                maxEmployees: 50,
                annualDiscountPct: 10.0,
                isActive: true,
                modules: [
                    [
                        'module_id' => $module->id,
                        'is_included' => true,
                        'is_available' => true,
                    ],
                ],
                addOns: [
                    [
                        'add_on_id' => $addon->id,
                        'is_available' => true,
                    ],
                ],
            )
        );

        $this->assertDatabaseHas('plan_tier_modules', [
            'plan_tier_id' => $plan->id,
            'module_id' => $module->id,
            'is_included' => true,
            'is_available' => true,
        ], 'conn_rhdpeople_geral');

        $this->assertDatabaseHas('plan_tier_add_ons', [
            'plan_tier_id' => $plan->id,
            'add_on_id' => $addon->id,
            'is_available' => true,
        ], 'conn_rhdpeople_geral');
    }

    public function test_it_throws_exception_when_plan_does_not_exist(): void
    {
        $this->expectException(
            \Illuminate\Database\Eloquent\ModelNotFoundException::class
        );

        $this->service->updatePlanTier(
            '00000000-0000-4000-8000-000000000000',
            new PlanTierDTO(
                name: 'Plano Inexistente',
                basePrice: null,
                maxEmployees: null,
                annualDiscountPct: null,
                isActive: null,
                modules: null,
                addOns: null,
            )
        );
    }
}