<?php

namespace App\Repositories\Contracts;

interface ModuleRepositoryInterface
{
    public function all();

    public function findById(string $id);

    public function create(array $data);

    public function update(string $id, array $data);

    public function delete(string $id);

    /**
     * Retorna todos os módulos ativos para montar a etapa 3 do configurador.
     */
    public function getAllActive();

    /**
     * Retorna os módulos inclusos em um tier específico.
     */
    public function getIncludedByPlanTier(string $planTierId);

    /**
     * Retorna os módulos disponíveis para contratação em um tier específico.
     */
    public function getAvailableByPlanTier(string $planTierId);
}
