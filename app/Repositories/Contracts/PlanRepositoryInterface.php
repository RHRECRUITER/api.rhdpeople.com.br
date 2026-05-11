<?php

namespace App\Repositories\Contracts;

interface PlanRepositoryInterface
{
    public function all();
    public function findById(string $id);
    public function create(array $data);
    public function update(string $id, array $data);
    public function delete(string $id);

    /**
     * Retorna o plano com todos os relacionamentos para montar o resumo.
     */
    public function getFullSummary(string $id);

    /**
     * Retorna todos os usuários vinculados ao plano via licenças.
     */
    public function getAllUsersInPlan(string $planId);

    /**
     * Filtra planos por status.
     */
    public function getByStatus(string $status);

    /**
     * Retorna o plano ativo de um tenant.
     */
    public function getActivePlanByTenant(string $tenantId);
}
