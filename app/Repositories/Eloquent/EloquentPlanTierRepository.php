<?php

namespace App\Repositories\Eloquent;

use App\Models\PlanTier;
use App\Repositories\Contracts\PlanTierRepositoryInterface;

class EloquentPlanTierRepository implements PlanTierRepositoryInterface
{
    public function all()
    {
        return PlanTier::all();
    }

    public function findById(string $id)
    {
        return PlanTier::findOrFail($id);
    }

    public function create(array $data)
    {
        return PlanTier::create($data);
    }

    public function update(string $id, array $data)
    {
        $tier = PlanTier::findOrFail($id);
        $tier->update($data);

        return $tier;
    }

    public function delete(string $id)
    {
        $tier = PlanTier::findOrFail($id);
        $tier->delete();
    }

    /**
     * Retorna todos os tiers ativos para exibição no front.
     *
     * @return void
     */
    public function getAllActive()
    {
        return PlanTier::where('is_active', true)->get();
    }

    /**
     * Retorna o tier com seus módulos e add-ons para montar o card.
     *
     * @return void
     */
    public function getWithModulesAndAddOns(string $id)
    {
        return PlanTier::with([
            'tierModules.module',
            'tierAddOns.addOn',
            'employeeRanges',
        ])->findOrFail($id);
    }
}
