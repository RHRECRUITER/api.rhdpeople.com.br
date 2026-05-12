<?php

namespace App\Repositories\Eloquent;

use App\Enums\TypeLicense;
use App\Models\License;
use App\Repositories\Contracts\LicenseRepositoryInterface;
use Illuminate\Support\Facades\Log;

class EloquentLicenseRepository implements LicenseRepositoryInterface
{
    public function all()
    {
        return License::all();
    }

    public function findById(string $id)
    {
        return License::findOrFail($id);
    }

    public function create(array $data)
    {
        return License::create($data);
    }

    public function update(string $id, array $data)
    {
        $license = License::findOrFail($id);
        $license->update($data);

        return $license;
    }

    public function delete(string $id)
    {
        $license = License::findOrFail($id);
        $license->delete();
    }

    /**
     * Retorna todas as licenças de um plano por tipo.
     */
    public function getByType(string $planId, string $type)
    {
        return License::where('plan_id', $planId)
            ->where('type_license', $type)
            ->get();
    }

    /**
     * Conta licenças de um plano por tipo.
     */
    public function countByType(string $planId, string $type)
    {
        return License::where('plan_id', $planId)
            ->where('type_license', $type)
            ->count();
    }

    /**
     * Retorna todos os usuários com um tipo de licença específico.
     */
    public function getAllUsersByTypeLicense(string $planId, string $type)
    {
        return License::where('plan_id', $planId)
            ->where('type_license', $type)
            ->where('is_active', true)
            ->with('user')
            ->get()
            ->pluck('user');
    }

    /**
     * Conta licenças ativas de um plano.
     */
    public function countActiveByPlan(string $planId)
    {
        return License::where('plan_id', $planId)
            ->where('is_active', true)
            ->count();
    }

    /**
     * Retorna a licença do dono de um plano.
     */
    public function getDonoByPlan(string $planId)
    {
        return License::where('plan_id', $planId)
            ->where('type_license', TypeLicense::DONO)
            ->with('user')
            ->firstOrFail();
    }

    /**
     * Desativa uma licença — não permitido para licenças do tipo dono.
     */
    public function deactivate(string $id)
    {
        $license = License::findOrFail($id);

        if ($license->isDono()) {
            Log::warning('Tentativa de desativar licença do dono bloqueada.', [
                'license_id' => $id,
            ]);

            return $license;
        }

        $license->update(['is_active' => false]);

        return $license;
    }

    /**
     * Ativa uma licença.
     */
    public function activate(string $id)
    {
        $license = License::findOrFail($id);
        $license->update(['is_active' => true]);

        return $license;
    }
}
