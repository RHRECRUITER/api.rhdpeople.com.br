<?php

namespace App\Services\Admin;

use App\DTOs\Admin\ModuleDTO;
use App\Models\Module;

class ModuleManagementService
{
    /**
     * Cria um novo módulo no catálogo.
     */
    public function createModule(ModuleDTO $dto): Module
    {
        // Cria o registro diretamente no banco e já joga na variável
        $module = Module::create([
            'key' => $dto->key,
            'label' => $dto->label,
            'description' => $dto->description,
            'extra_price' => $dto->extraPrice,
            'is_active' => $dto->isActive,
        ]);

        // Retorna o objeto criado para o Controller
        return $module;
    }

    /**
     * Atualiza um módulo existente.
     */
    public function updateModule(string $id, ModuleDTO $dto): Module
    {
        // 1. Busca no banco (se não achar, o findOrFail já devolve erro 404 sozinho)
        $module = Module::findOrFail($id);

        // 2. Atualiza só o que foi enviado no Request
        $module->update(array_filter([
            'key' => $dto->key,
            'label' => $dto->label,
            'description' => $dto->description,
            'extra_price' => $dto->extraPrice,
            'is_active' => $dto->isActive,
        ], fn ($v) => ! is_null($v)));

        // 3. Retorna o Model atualizado
        return $module;
    }
}
