<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PlanTierModuleSeeder extends Seeder
{
    public function run(): void
    {
        $tierModules = [
            // ─── Start ───────────────────────────────────────────
            // Start não tem nenhum módulo incluso nem disponível como extra
            // apenas funcionalidades básicas do dashboard

            // ─── Growth ──────────────────────────────────────────
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0002-0002-0002-000000000002',
                'module_id'    => 'c1b2c3d4-0001-0001-0001-000000000001', // Controle de Ponto
                'is_included'  => true,
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0002-0002-0002-000000000002',
                'module_id'    => 'c1b2c3d4-0002-0002-0002-000000000002', // Férias & Licenças
                'is_included'  => true,
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0002-0002-0002-000000000002',
                'module_id'    => 'c1b2c3d4-0003-0003-0003-000000000003', // Folha de Pagamento
                'is_included'  => false,
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0002-0002-0002-000000000002',
                'module_id'    => 'c1b2c3d4-0004-0004-0004-000000000004', // Benefícios
                'is_included'  => true,
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0002-0002-0002-000000000002',
                'module_id'    => 'c1b2c3d4-0005-0005-0005-000000000005', // Documentos e Assinaturas
                'is_included'  => true,
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0002-0002-0002-000000000002',
                'module_id'    => 'c1b2c3d4-0006-0006-0006-000000000006', // Relatórios e Auditoria
                'is_included'  => false,
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],

            // ─── Pro ─────────────────────────────────────────────
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0003-0003-0003-000000000003',
                'module_id'    => 'c1b2c3d4-0001-0001-0001-000000000001', // Controle de Ponto
                'is_included'  => true,
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0003-0003-0003-000000000003',
                'module_id'    => 'c1b2c3d4-0002-0002-0002-000000000002', // Férias & Licenças
                'is_included'  => true,
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0003-0003-0003-000000000003',
                'module_id'    => 'c1b2c3d4-0003-0003-0003-000000000003', // Folha de Pagamento
                'is_included'  => true,
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0003-0003-0003-000000000003',
                'module_id'    => 'c1b2c3d4-0004-0004-0004-000000000004', // Benefícios
                'is_included'  => true,
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0003-0003-0003-000000000003',
                'module_id'    => 'c1b2c3d4-0005-0005-0005-000000000005', // Documentos e Assinaturas
                'is_included'  => true,
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0003-0003-0003-000000000003',
                'module_id'    => 'c1b2c3d4-0006-0006-0006-000000000006', // Relatórios e Auditoria
                'is_included'  => true,
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],

            // ─── Enterprise ──────────────────────────────────────
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0004-0004-0004-000000000004',
                'module_id'    => 'c1b2c3d4-0001-0001-0001-000000000001', // Controle de Ponto
                'is_included'  => true,
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0004-0004-0004-000000000004',
                'module_id'    => 'c1b2c3d4-0002-0002-0002-000000000002', // Férias & Licenças
                'is_included'  => true,
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0004-0004-0004-000000000004',
                'module_id'    => 'c1b2c3d4-0003-0003-0003-000000000003', // Folha de Pagamento
                'is_included'  => true,
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0004-0004-0004-000000000004',
                'module_id'    => 'c1b2c3d4-0004-0004-0004-000000000004', // Benefícios
                'is_included'  => true,
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0004-0004-0004-000000000004',
                'module_id'    => 'c1b2c3d4-0005-0005-0005-000000000005', // Documentos e Assinaturas
                'is_included'  => true,
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => Str::uuid(),
                'plan_tier_id' => 'a1b2c3d4-0004-0004-0004-000000000004',
                'module_id'    => 'c1b2c3d4-0006-0006-0006-000000000006', // Relatórios e Auditoria
                'is_included'  => true,
                'is_available' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ];

        DB::table('plan_tier_modules')->insertOrIgnore($tierModules);
    }
}
