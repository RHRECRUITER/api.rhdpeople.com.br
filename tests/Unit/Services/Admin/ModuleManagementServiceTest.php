<?php

namespace Tests\Unit\Services\Admin;

use App\DTOs\Admin\ModuleDTO;
use App\Models\Module;
use App\Services\Admin\ModuleManagementService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ModuleManagementServiceTest extends TestCase
{
    use DatabaseTransactions;

    private ModuleManagementService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new ModuleManagementService();
    }

    public function test_it_creates_a_module(): void
    {
        $dto = new ModuleDTO(
            key: 'reports',
            label: 'Relatórios',
            description: 'Módulo de relatórios',
            extraPrice: 29.90,
            isActive: true,
        );

        $module = $this->service->createModule($dto);

        $this->assertInstanceOf(Module::class, $module);

        $this->assertDatabaseHas('modules', [
            'id' => $module->id,
            'key' => 'reports',
            'label' => 'Relatórios',
            'description' => 'Módulo de relatórios',
            'extra_price' => 29.90,
            'is_active' => true,
        ]);
    }

    public function test_it_updates_a_module(): void
    {
        $module = $this->service->createModule(
            new ModuleDTO(
                key: 'reports',
                label: 'Relatórios',
                description: 'Descrição antiga',
                extraPrice: 10.00,
                isActive: true,
            )
        );

        $updated = $this->service->updateModule(
            (string) $module->id,
            new ModuleDTO(
                key: null,
                label: 'Relatórios avançados',
                description: null,
                extraPrice: null,
                isActive: false,
            )
        );

        $this->assertSame($module->id, $updated->id);

        $this->assertDatabaseHas('modules', [
            'id' => $module->id,
            'key' => 'reports',
            'label' => 'Relatórios avançados',
            'description' => 'Descrição antiga',
            'extra_price' => 10.00,
            'is_active' => false,
        ]);
    }

    public function test_it_throws_exception_when_module_does_not_exist(): void
    {
        $this->expectException(
            \Illuminate\Database\Eloquent\ModelNotFoundException::class
        );

        $this->service->updateModule(
            '00000000-0000-4000-8000-000000000000',
            new ModuleDTO(
                key: 'reports',
                label: null,
                description: null,
                extraPrice: null,
                isActive: null,
            )
        );
    }
}
