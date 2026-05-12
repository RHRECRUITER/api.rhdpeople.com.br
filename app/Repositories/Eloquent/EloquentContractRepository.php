<?php

namespace App\Repositories\Eloquent;

use App\Models\Contract;
use App\Repositories\Contracts\ContractRepositoryInterface;

class EloquentContractRepository implements ContractRepositoryInterface
{
    public function all()
    {
        return Contract::all();
    }

    public function findById(string $id)
    {
        return Contract::findOrFail($id);
    }

    public function create(array $data)
    {
        return Contract::create($data);
    }

    public function update(string $id, array $data)
    {
        $contract = Contract::findOrFail($id);
        $contract->update($data);

        return $contract;
    }

    public function delete(string $id)
    {
        $contract = Contract::findOrFail($id);
        $contract->delete();
    }

    /**
     * Retorna todos os contratos vencidos.
     */
    public function filterAllExpired()
    {
        return Contract::where('expiration_date', '<', now())->get();
    }

    /**
     * Retorna contratos filtrando pelo ciclo de cobrança.
     */
    public function filterByBillingCycle(string $billingCycle)
    {
        return Contract::where('billing_cycle', $billingCycle)->get();
    }

    /**
     * Retorna contratos que vencem nos próximos N dias.
     */
    public function getDueSoon(int $days = 30)
    {
        return Contract::whereBetween('expiration_date', [
            now(),
            now()->addDays($days),
        ])->get();
    }
}
