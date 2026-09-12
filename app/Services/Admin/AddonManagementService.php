<?php
namespace App\Services\Admin;

use app\DTOs\Admin\addonDTO;
use app\Models\AddOn;

class AddonManagementService
{
    public function createAddon(addonDTO $dto): AddOn
    {
        $addon = AddOn::create([
            'key'         => $dto->key,
            'label'       => $dto->label,
            'description' => $dto->description,
            'extra_price' => $dto->extraPrice,
            'is_active'   => $dto->isActive,
        ]);
        return $addon;
    }
    public function updateModule(string $id, addonDTO $dto): AddOn
    {

        $addon = AddOn::findOrFail($id);

        $addon->update(array_filter([
            'key'         => $dto->key,
            'label'       => $dto->label,
            'description' => $dto->description,
            'extra_price' => $dto->extraPrice,
            'is_active'   => $dto->isActive,
        ], fn($v) => ! is_null($v)));

        return $addon;
    }
}
