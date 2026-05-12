<?php

namespace App\Repositories\Eloquent;

use App\Models\Plan;
use App\Repositories\Contracts\PlanRepositoryInterface;

class EloquentPlanRepository implements PlanRepositoryInterface
{
    public function all()
    {
        return Plan::all();
    }

    public function findById(string $id)
    {
        return Plan::findOrFail($id);
    }

    public function create(array $data)
    {
        return Plan::create($data);
    }

    public function update(string $id, array $data)
    {
        $plan = Plan::findOrFail($id);
        $plan->update($data);

        return $plan;
    }

    public function delete(string $id)
    {
        $plan = Plan::findOrFail($id);
        $plan->delete();
    }

    /**
     * Retorna o plano com todos os relacionamentos para montar o resumo.
     */
    public function getFullSummary(string $id)
    {
        return Plan::with([
            'planTier',
            'employeeRange',
            'contract',
            'planModules.module',
            'planAddOns.addOn',
            'licenses.user',
        ])->findOrFail($id);
    }

    /**
     * Retorna todos os usuários vinculados ao plano via licenças.
     */
    public function getAllUsersInPlan(string $planId)
    {
        return Plan::findOrFail($planId)
            ->licenses()
            ->with('user')
            ->where('is_active', true)
            ->get()
            ->pluck('user');
    }

    /**
     * Filtra planos por status.
     */
    public function getByStatus(string $status)
    {
        return Plan::where('status', $status)->get();
    }

    /**
     * Retorna o plano ativo de um tenant.
     */
    public function getActivePlanByTenant(string $tenantId)
    {
        return Plan::where('tenant_id', $tenantId)
            ->whereIn('status', ['active', 'trial'])
            ->with([
                'planTier',
                'employeeRange',
                'planModules.module',
                'planAddOns.addOn',
            ])
            ->first();
    }
}
