<?php

namespace App\Repositories\Eloquent;

use App\Models\Module;
use App\Repositories\Contracts\ModuleRepositoryInterface;

class EloquentModuleRepository implements ModuleRepositoryInterface
{
    public function all()
    {
        return Module::all();
    }

    public function findById(string $id)
    {
        return Module::findOrFail($id);
    }

    public function create(array $data)
    {
        return Module::create($data);
    }

    public function update(string $id, array $data)
    {
        $module = Module::findOrFail($id);
        $module->update($data);
        return $module;
    }

    public function delete(string $id)
    {
        $module = Module::findOrFail($id);
        $module->delete();
    }

    /**
     * Retorna todos os módulos ativos para montar a etapa 3 do configurador.
     */
    public function getAllActive()
    {
        return Module::where('is_active', true)->get();
    }

    /**
     * Retorna os módulos inclusos em um tier específico.
     */
    public function getIncludedByPlanTier(string $planTierId)
    {
        return Module::whereHas('tierModules', function ($query) use ($planTierId) {
            $query->where('plan_tier_id', $planTierId)
                  ->where('is_included', true)
                  ->where('is_available', true);
        })->get();
    }

    /**
     * Retorna os módulos disponíveis para contratação em um tier específico.
     */
    public function getAvailableByPlanTier(string $planTierId)
    {
        return Module::whereHas('tierModules', function ($query) use ($planTierId) {
            $query->where('plan_tier_id', $planTierId)
                  ->where('is_available', true);
        })->get();
    }
}
