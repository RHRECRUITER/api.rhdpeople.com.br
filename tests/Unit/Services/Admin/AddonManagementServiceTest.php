<?php

namespace Tests\Unit\Services\Admin;

use App\DTOs\Admin\AddOnDTO;
use App\Models\AddOn;
use App\Services\Admin\AddonManagementService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AddonManagementServiceTest extends TestCase
{
   use DatabaseTransactions;

    protected $connectionsToTransact = ['conn_rhdpeople_geral'];

    private AddonManagementService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new AddonManagementService();
    }

    public function test_it_creates_an_addon(): void
    {
        $dto = new AddOnDTO(
            key: 'extra_storage',
            label: 'Armazenamento extra',
            description: 'Espaço adicional',
            extraPrice: 19.90,
            isActive: true,
        );

        $addon = $this->service->createAddon($dto);

        $this->assertInstanceOf(AddOn::class, $addon);

        $this->assertDatabaseHas('add_ons', [
            'id' => $addon->id,
            'key' => 'extra_storage',
            'label' => 'Armazenamento extra',
            'description' => 'Espaço adicional',
            'price' => 19.90,
            'is_active' => true,
        ]);
    }

    public function test_it_updates_an_addon(): void
    {
        $addon = $this->service->createAddon(
            new AddOnDTO(
                key: 'extra_storage',
                label: 'Armazenamento',
                description: 'Descrição antiga',
                extraPrice: 10.00,
                isActive: true,
            )
        );

        $updated = $this->service->updateAddOn(
            (string) $addon->id,
            new AddOnDTO(
                key: null,
                label: 'Armazenamento adicional',
                description: null,
                extraPrice: null,
                isActive: false,
            )
        );

        $this->assertSame($addon->id, $updated->id);

        $this->assertDatabaseHas('add_ons', [
            'id' => $addon->id,
            'key' => 'extra_storage',
            'label' => 'Armazenamento adicional',
            'description' => 'Descrição antiga',
            'price' => 10.00,
            'is_active' => false,
        ]);
    }

    public function test_it_throws_exception_when_addon_does_not_exist(): void
    {
        $this->expectException(
            \Illuminate\Database\Eloquent\ModelNotFoundException::class
        );

        $this->service->updateAddOn(
            '00000000-0000-4000-8000-000000000000',
            new AddOnDTO(
                key: 'extra_storage',
                label: null,
                description: null,
                extraPrice: null,
                isActive: null,
            )
        );
    }
}