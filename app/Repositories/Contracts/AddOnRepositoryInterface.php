<?php

namespace App\Repositories\Contracts;

interface AddOnRepositoryInterface
{
    public function all();
    public function findById(string $id);
    public function create(array $data);
    public function update(string $id, array $data);
    public function delete(string $id);

    /**
     * Retorna todos os add-ons ativos para montar a etapa 4 do configurador.
     */
    public function getAllActive();

    /**
     * Retorna os add-ons disponíveis para um tier específico.
     */
    public function getAvailableByPlanTier(string $planTierId);
}
