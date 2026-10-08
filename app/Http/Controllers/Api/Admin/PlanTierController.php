<?php

namespace App\Http\Controllers\Api\Admin;

use App\DTOs\Admin\PlanTierDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Plan\StorePlanTierRequest;
use App\Http\Requests\Admin\Plan\UpdatePlanTierRequest;
use App\Models\PlanTier;
use App\Services\Admin\PlanTierManagementService;
use Illuminate\Support\Facades\Response;

class PlanTierController extends Controller
{
    public function __construct(
        private readonly PlanTierManagementService $planTierService
    ) {}

    /**
     * Listagem de todos os planos cadastrados (Catálogo)
     * GET /api/v1/admin/plan-tiers
     */
    public function index()
    {
        $planTiers = PlanTier::with(['tierModules.module', 'tierAddOns.addOn'])
            ->orderBy('base_price', 'asc')
            ->get();

        return Response::api([
            'plan_tiers' => $planTiers,
        ], 'Catálogo de planos recuperado com sucesso.');
    }

    /**
     * Exibe os detalhes de um plano específico
     * GET /api/v1/admin/plan-tiers/{id}
     */
    public function show(string $id)
    {
        $planTier = PlanTier::with(['tierModules.module', 'tierAddOns.addOn'])
            ->findOrFail($id);

        return Response::api([
            'plan_tier' => $planTier,
        ], 'Detalhes do plano recuperados com sucesso.');
    }

    /**
     * Cadastra um novo plano no catálogo
     * POST /api/v1/admin/plan-tiers
     */
    public function store(StorePlanTierRequest $request)
    {
        // 1. Converte a Request validada em DTO
        $dto = PlanTierDTO::fromRequest($request);

        // 2. Executa a regra de negócio no Service
        $planTier = $this->planTierService->createPlanTier($dto);

        // 3. Devolve a resposta padronizada (201 Created)
        return Response::api([
            'plan_tier' => $planTier,
        ], 'Plano parametrizado com sucesso.', 201);
    }

    /**
     * Atualiza um plano existente
     * PUT /api/v1/admin/plan-tiers/{id}
     */
    public function update(UpdatePlanTierRequest $request, string $id)
    {
        $dto = PlanTierDTO::fromRequest($request);

        $planTier = $this->planTierService->updatePlanTier($id, $dto);

        return Response::api([
            'plan_tier' => $planTier,
        ], 'Plano atualizado com sucesso.', 200);
    }

    /**
     * Ativa ou Inativa a comercialização de um plano
     * PATCH /api/v1/admin/plan-tiers/{id}/status
     */
    public function toggleStatus(string $id)
    {
        $planTier = PlanTier::findOrFail($id);

        $planTier->update([
            'is_active' => ! $planTier->is_active,
        ]);

        $statusMessage = $planTier->is_active ? 'ativado' : 'inativado';

        return Response::api([
            'plan_tier' => $planTier,
        ], "Plano {$statusMessage} com sucesso.");
    }
}
