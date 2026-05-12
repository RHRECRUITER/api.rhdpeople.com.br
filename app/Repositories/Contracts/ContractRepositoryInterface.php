<?php

namespace App\Repositories\Contracts;

interface ContractRepositoryInterface
{
    public function all();

    public function findById(string $id);

    public function create(array $data);

    public function update(string $id, array $data);

    public function delete(string $id);

    /**
     * Retorna todos os contratos vencidos.
     */
    public function filterAllExpired();

    /**
     * Retorna contratos filtrando pelo ciclo de cobrança.
     */
    public function filterByBillingCycle(string $billingCycle);

    /**
     * Retorna contratos que vencem nos próximos N dias.
     */
    public function getDueSoon(int $days = 30);
}
