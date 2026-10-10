<?php

namespace app\Services\Auth;

use App\DTOs\Auth\RegisterOwnerDTO;
use App\Enums\TypeLicense;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use App\Repositories\Contracts\ContractRepositoryInterface;
use App\Repositories\Contracts\LicenseRepositoryInterface;
use App\Repositories\Contracts\PlanRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterAndContractService
{
    public function __construct(
        private UserRepositoryInterface $userRepo,
        private CompanyRepositoryInterface $companyRepo,
        private ContractRepositoryInterface $contractRepo,
        private PlanRepositoryInterface $planRepo,
        private LicenseRepositoryInterface $licenseRepo
    ) {}

    public function registerOwner(RegisterOwnerDTO $dto)
    {
        return DB::transaction(function () use ($dto) {
            // criação de user
            $user = $this->userRepo->create([
                'name' => $dto->userData['name'],
                'email' => $dto->userData['email'],
                'password' => Hash::make($dto->userData['password']),
            ]);
            // Criação da empresa
            $company = $this->companyRepo->create([
                'name' => $dto->companyData['name'],
                'document' => $dto->companyData['cnpj'],
                'email' => $dto->companyData['email'],
                'phone' => $dto->companyData['phone'],
                'status' => 'active',
            ]);
            // Criação do contrato
            $contract = $this->contractRepo->create([
                'tenant_id' => $company->id,
                'start_date' => Carbon::today(),             // data de aquisição do plano
                'expiration_date' => Carbon::today()->addMonth(), // data de validade do plano por default 1mes. Definir logica de criação e renovação pos pagamento.
                'billing_cycle' => 'monthly',                   // por padrão setado ciclo mensal
                'auto_renew' => true,                        // renovação automatica ate definição de regras de renovação
            ]);

            // Criação do plano de assinatura base
            $plan = $this->planRepo->create([
                'tenant_id' => $company->id,
                'contract_id' => $contract->id,
                'plan_tier_id' => $dto->planData['plan_tier_id'],
                'employee_range_id' => $dto->planData['employee_range_id'],
                'billing_cycle' => 'monthly',
                'price_base' => $dto->planData['price_total'],
                'price_with_multiplier' => $dto->planData['price_total'],
                'price_subtotal' => $dto->planData['price_total'],
                'price_total' => $dto->planData['price_total'],
                'status' => 'active',
            ]);
            // Criação da licença Dono
            $license = $this->licenseRepo->create([
                'plan_id' => $plan->id,
                'user_id' => $user->id,
                'type_license' => TypeLicense::DONO->value,
                'status' => 'active',
            ]);

            return [
                'user' => $user,
                'company' => $company,
                'license' => $license,
            ];
        });

    }
}
