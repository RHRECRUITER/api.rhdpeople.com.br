<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PlanTierAddOnSeeder extends Seeder
{
    public function run(): void
    {
        $tierAddOns = [
            // ─── Start ───────────────────────────────────────────
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0001-0001-0001-000000000001',
                'add_on_id'    => 'd1b2c3d4-0001-0001-0001-000000000001', // KYC biometria
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0001-0001-0001-000000000001',
                'add_on_id'    => 'd1b2c3d4-0002-0002-0002-000000000002', // GPS geofencing
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0001-0001-0001-000000000001',
                'add_on_id'    => 'd1b2c3d4-0003-0003-0003-000000000003', // Assinatura digital
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0001-0001-0001-000000000001',
                'add_on_id'    => 'd1b2c3d4-0004-0004-0004-000000000004', // Exportação avançada
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],

            // ─── Growth ──────────────────────────────────────────
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0002-0002-0002-000000000002',
                'add_on_id'    => 'd1b2c3d4-0001-0001-0001-000000000001', // KYC biometria
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0002-0002-0002-000000000002',
                'add_on_id'    => 'd1b2c3d4-0002-0002-0002-000000000002', // GPS geofencing
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0002-0002-0002-000000000002',
                'add_on_id'    => 'd1b2c3d4-0003-0003-0003-000000000003', // Assinatura digital
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0002-0002-0002-000000000002',
                'add_on_id'    => 'd1b2c3d4-0004-0004-0004-000000000004', // Exportação avançada
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0002-0002-0002-000000000002',
                'add_on_id'    => 'd1b2c3d4-0005-0005-0005-000000000005', // Integrações contábeis
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0002-0002-0002-000000000002',
                'add_on_id'    => 'd1b2c3d4-0006-0006-0006-000000000006', // Trilha auditoria avançada
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0002-0002-0002-000000000002',
                'add_on_id'    => 'd1b2c3d4-0007-0007-0007-000000000007', // Suporte premium + SLA
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],

            // ─── Pro ─────────────────────────────────────────────
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0003-0003-0003-000000000003',
                'add_on_id'    => 'd1b2c3d4-0001-0001-0001-000000000001', // KYC biometria
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0003-0003-0003-000000000003',
                'add_on_id'    => 'd1b2c3d4-0002-0002-0002-000000000002', // GPS geofencing
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0003-0003-0003-000000000003',
                'add_on_id'    => 'd1b2c3d4-0003-0003-0003-000000000003', // Assinatura digital
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0003-0003-0003-000000000003',
                'add_on_id'    => 'd1b2c3d4-0004-0004-0004-000000000004', // Exportação avançada
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0003-0003-0003-000000000003',
                'add_on_id'    => 'd1b2c3d4-0005-0005-0005-000000000005', // Integrações contábeis
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0003-0003-0003-000000000003',
                'add_on_id'    => 'd1b2c3d4-0006-0006-0006-000000000006', // Trilha auditoria avançada
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0003-0003-0003-000000000003',
                'add_on_id'    => 'd1b2c3d4-0007-0007-0007-000000000007', // Suporte premium + SLA
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0003-0003-0003-000000000003',
                'add_on_id'    => 'd1b2c3d4-0008-0008-0008-000000000008', // SSO
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],

            // ─── Enterprise ──────────────────────────────────────
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0004-0004-0004-000000000004',
                'add_on_id'    => 'd1b2c3d4-0001-0001-0001-000000000001', // KYC biometria
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0004-0004-0004-000000000004',
                'add_on_id'    => 'd1b2c3d4-0002-0002-0002-000000000002', // GPS geofencing
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0004-0004-0004-000000000004',
                'add_on_id'    => 'd1b2c3d4-0003-0003-0003-000000000003', // Assinatura digital
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0004-0004-0004-000000000004',
                'add_on_id'    => 'd1b2c3d4-0004-0004-0004-000000000004', // Exportação avançada
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0004-0004-0004-000000000004',
                'add_on_id'    => 'd1b2c3d4-0005-0005-0005-000000000005', // Integrações contábeis
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0004-0004-0004-000000000004',
                'add_on_id'    => 'd1b2c3d4-0006-0006-0006-000000000006', // Trilha auditoria avançada
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0004-0004-0004-000000000004',
                'add_on_id'    => 'd1b2c3d4-0007-0007-0007-000000000007', // Suporte premium + SLA
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0004-0004-0004-000000000004',
                'add_on_id'    => 'd1b2c3d4-0008-0008-0008-000000000008', // SSO
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ];

        DB::table('plan_tier_add_ons')->insertOrIgnore($tierAddOns);
    }
}
