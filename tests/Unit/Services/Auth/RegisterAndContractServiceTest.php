<?php

namespace Tests\Unit\Services\Auth;

use App\DTOs\Auth\RegisterOwnerDTO;
use App\Enums\TypeLicense;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use App\Repositories\Contracts\ContractRepositoryInterface;
use App\Repositories\Contracts\LicenseRepositoryInterface;
use App\Repositories\Contracts\PlanRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Auth\RegisterAndContractService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;
use Throwable;

class RegisterAndContractServiceTest extends TestCase
{
    private UserRepositoryInterface $userRepo;
    private CompanyRepositoryInterface $companyRepo;
    private ContractRepositoryInterface $contractRepo;
    private PlanRepositoryInterface $planRepo;
    private LicenseRepositoryInterface $licenseRepo;

    private RegisterAndContractService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepo = Mockery::mock(UserRepositoryInterface::class);
        $this->companyRepo = Mockery::mock(CompanyRepositoryInterface::class);
        $this->contractRepo = Mockery::mock(ContractRepositoryInterface::class);
        $this->planRepo = Mockery::mock(PlanRepositoryInterface::class);
        $this->licenseRepo = Mockery::mock(LicenseRepositoryInterface::class);

        $this->service = new RegisterAndContractService(
            $this->userRepo,
            $this->companyRepo,
            $this->contractRepo,
            $this->planRepo,
            $this->licenseRepo
        );
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        Mockery::close();

        parent::tearDown();
    }

    private function makeDto(): RegisterOwnerDTO
    {
        return new RegisterOwnerDTO(
            userData: [
                'name' => 'Lucas',
                'email' => 'lucas@example.com',
                'password' => 'Senha123!',
            ],
            companyData: [
                'name' => 'Empresa Teste',
                'cnpj' => '12345678000199',
                'email' => 'empresa@example.com',
                'phone' => '83999999999',
            ],
            planData: [
                'plan_tier_id' => 'tier-uuid',
                'employee_range_id' => 'range-uuid',
                'price_total' => 199.90,
            ]
        );
    }

    public function test_registra_usuario_empresa_contrato_plano_e_licenca(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-21'));

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(fn (callable $callback) => $callback());

        Hash::shouldReceive('make')
            ->once()
            ->with('Senha123!')
            ->andReturn('senha-hash');

        $user = (object) ['id' => 'user-id'];
        $company = (object) ['id' => 'company-id'];
        $contract = (object) ['id' => 'contract-id'];
        $plan = (object) ['id' => 'plan-id'];
        $license = (object) ['id' => 'license-id'];

        $this->userRepo
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function (array $data): bool {
                return $data['name'] === 'Lucas'
                    && $data['email'] === 'lucas@example.com'
                    && $data['password'] === 'senha-hash';
            }))
            ->andReturn($user);

        $this->companyRepo
            ->shouldReceive('create')
            ->once()
            ->with([
                'name' => 'Empresa Teste',
                'document' => '12345678000199',
                'email' => 'empresa@example.com',
                'phone' => '83999999999',
                'status' => 'active',
            ])
            ->andReturn($company);

        $this->contractRepo
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function (array $data): bool {
                return $data['tenant_id'] === 'company-id'
                    && $data['start_date']->toDateString() === '2026-10-21'
                    && $data['expiration_date']->toDateString() === '2026-11-21'
                    && $data['billing_cycle'] === 'monthly'
                    && $data['auto_renew'] === true;
            }))
            ->andReturn($contract);

        $this->planRepo
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function (array $data): bool {
                return $data['tenant_id'] === 'company-id'
                    && $data['contract_id'] === 'contract-id'
                    && $data['plan_tier_id'] === 'tier-uuid'
                    && $data['employee_range_id'] === 'range-uuid'
                    && $data['billing_cycle'] === 'monthly'
                    && $data['price_base'] == 199.90
                    && $data['price_with_multiplier'] == 199.90
                    && $data['price_subtotal'] == 199.90
                    && $data['price_total'] == 199.90
                    && $data['status'] === 'active';
            }))
            ->andReturn($plan);

        $this->licenseRepo
            ->shouldReceive('create')
            ->once()
            ->with([
                'plan_id' => 'plan-id',
                'user_id' => 'user-id',
                'type_license' => TypeLicense::DONO->value,
                'status' => 'active',
            ])
            ->andReturn($license);

        $result = $this->service->registerOwner($this->makeDto());

        $this->assertSame($user, $result['user']);
        $this->assertSame($company, $result['company']);
        $this->assertSame($license, $result['license']);
    }

    public function test_propaga_excecao_quando_a_criacao_falha(): void
    {
        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(fn (callable $callback) => $callback());

        Hash::shouldReceive('make')
            ->once()
            ->with('Senha123!')
            ->andReturn('senha-hash');

        $this->userRepo
            ->shouldReceive('create')
            ->once()
            ->andReturn((object) ['id' => 'user-id']);

        $this->companyRepo
            ->shouldReceive('create')
            ->once()
            ->andThrow(new \RuntimeException('Falha ao criar empresa'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Falha ao criar empresa');

        $this->service->registerOwner($this->makeDto());
    }
}
