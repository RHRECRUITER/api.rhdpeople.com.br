<?php

namespace App\Services\Admin;

use App\DTOs\Admin\PlanTierDTO;
use App\Models\PlanTier;
use Illuminate\Support\Facades\DB;
use Throwable;

class PlanTierManagementService
{
    /**
     * Cria um novo Plano e sincroniza seus módulos e add-ons.
     */
    public function createPlanTier(PlanTierDTO $dto): PlanTier
    {
        DB::beginTransaction();

        try {
            // 1. Criar o registro Pai com as propriedades corretas do DTO
            $planTier = PlanTier::create([
                'name' => $dto->name,
                'base_price' => $dto->basePrice,
                'max_employees' => $dto->maxEmployees,
                'annual_discount_pct' => $dto->annualDiscountPct,
                'is_active' => $dto->isActive,
            ]);

            // 2. Módulos
            if (! empty($dto->modules)) {
                foreach ($dto->modules as $moduleData) {
                    $planTier->tierModules()->create([
                        'module_id' => $moduleData['module_id'],
                        'is_included' => $moduleData['is_included'] ?? false,
                        'is_available' => $moduleData['is_available'] ?? true,
                    ]);
                }
            }

            // 3. Add-ons
            if (! empty($dto->addOns)) {
                foreach ($dto->addOns as $addOnData) {
                    $planTier->tierAddOns()->create([
                        'add_on_id' => $addOnData['add_on_id'],
                        'is_available' => $addOnData['is_available'] ?? true,
                    ]);
                }
            }

            DB::commit();

            return $planTier->load('tierModules', 'tierAddOns');

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Atualiza um Plano existente.
     */
    public function updatePlanTier(string $id, PlanTierDTO $dto): PlanTier
    {
        $planTier = PlanTier::findOrFail($id);

        DB::beginTransaction();

        try {
            // 1. Atualizar dados base
            $planTier->update(array_filter([
                'name' => $dto->name,
                'base_price' => $dto->basePrice,
                'max_employees' => $dto->maxEmployees,
                'annual_discount_pct' => $dto->annualDiscountPct,
                'is_active' => $dto->isActive,
            ], fn ($v) => ! is_null($v)));

            // 2. Atualizar Módulos (Deleta antigos e insere novos)
            if ($dto->modules !== null) {
                $planTier->tierModules()->delete();

                foreach ($dto->modules as $moduleData) {
                    $planTier->tierModules()->create([
                        'module_id' => $moduleData['module_id'],
                        'is_included' => $moduleData['is_included'] ?? false,
                        'is_available' => $moduleData['is_available'] ?? true,
                    ]);
                }
            }

            // 3. Atualizar Add-ons (Deleta antigos e insere novos)
            if ($dto->addOns !== null) {
                $planTier->tierAddOns()->delete();

                foreach ($dto->addOns as $addOnData) {
                    $planTier->tierAddOns()->create([
                        'add_on_id' => $addOnData['add_on_id'],
                        'is_available' => $addOnData['is_available'] ?? true,
                    ]);
                }
            }

            DB::commit();

            return $planTier->load('tierModules', 'tierAddOns');

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
