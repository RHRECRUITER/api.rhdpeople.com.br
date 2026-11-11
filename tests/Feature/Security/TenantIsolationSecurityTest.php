<?php

namespace Tests\Feature\Security;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class TenantIsolationSecurityTest extends TestCase
{
    protected function tearDown(): void
    {
        Auth::logout();

        parent::tearDown();
    }

    public function test_regular_user_receives_tenant_global_scope(): void
    {
        $user = new User();

        // Simula os atributos utilizados pelo trait legado.
        $user->setAttribute('type', 'company_user');
        $user->setAttribute('tenant_id', 'tenant-a');

        Auth::login($user);

        $this->assertTrue(
            $this->queryHasTenantScope(),
            'A consulta deveria possuir o escopo de tenant.'
        );
    }

    public function test_super_admin_does_not_receive_tenant_global_scope(): void
    {
        $user = new User();

        $user->setAttribute('type', 'super_admin');
        $user->setAttribute('tenant_id', null);

        Auth::login($user);

        $this->assertFalse(
            $this->queryHasTenantScope(),
            'O superadministrador não deveria receber o escopo de tenant.'
        );
    }

    private function queryHasTenantScope(): bool
    {
        $wheres = Plan::query()
            ->applyScopes()
            ->getQuery()
            ->wheres ?? [];

        return collect($wheres)->contains(
            fn ($where) => isset($where['column'])
                && is_string($where['column'])
                && str_ends_with($where['column'], 'tenant_id')
        );
    }
}