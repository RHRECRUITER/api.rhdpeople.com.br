<?php

namespace App\Repositories\Eloquent;

use App\Models\AddOn;
use App\Repositories\Contracts\AddOnRepositoryInterface;

class EloquentAddOnRepository implements AddOnRepositoryInterface
{
    public function all()
    {
        return AddOn::all();
    }

    public function findById(string $id)
    {
        return AddOn::findOrFail($id);
    }

    public function create(array $data)
    {
        return AddOn::create($data);
    }

    public function update(string $id, array $data)
    {
        $addOn = AddOn::findOrFail($id);
        $addOn->update($data);
        return $addOn;
    }

    public function delete(string $id)
    {
        $addOn = AddOn::findOrFail($id);
        $addOn->delete();
    }

    /**
     * Retorna todos os add-ons ativos para montar a etapa 4 do configurador.
     */
    public function getAllActive()
    {
        return AddOn::where('is_active', true)->get();
    }

    /**
     * Retorna os add-ons disponíveis para um tier específico.
     */
    public function getAvailableByPlanTier(string $planTierId)
    {
        return AddOn::whereHas('tierAddOns', function ($query) use ($planTierId) {
            $query->where('plan_tier_id', $planTierId)
                  ->where('is_available', true);
        })->get();
    }
}
