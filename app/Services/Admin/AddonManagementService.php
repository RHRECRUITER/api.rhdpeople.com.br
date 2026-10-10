<?php

namespace App\Services\Admin;

use App\DTOs\Admin\AddonDTO;
use App\Models\AddOn;

class AddonManagementService
{
    public function createAddon(AddonDTO $dto): AddOn
    {
        $addon = AddOn::create([
            'key' => $dto->key,
            'label' => $dto->label,
            'description' => $dto->description,
            'price' => $dto->price,
            'is_active' => $dto->isActive,
        ]);

        return $addon;
    }

    public function updateAddon(string $id, AddonDTO $dto): AddOn
    {

        $addon = AddOn::findOrFail($id);

        $addon->update(array_filter([
            'key' => $dto->key,
            'label' => $dto->label,
            'description' => $dto->description,
            'price' => $dto->price,
            'is_active' => $dto->isActive,
        ], fn ($v) => ! is_null($v)));

        return $addon;
    }
}
