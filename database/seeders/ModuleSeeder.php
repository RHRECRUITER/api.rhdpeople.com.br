<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            [
                'id'          => 'c1b2c3d4-0001-0001-0001-000000000001',
                'key'         => 'time_tracking',
                'label'       => 'Controle de Ponto',
                'extra_price' => null,
                'description' => 'Registro e controle de ponto dos colaboradores.',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 'c1b2c3d4-0002-0002-0002-000000000002',
                'key'         => 'vacations_leaves',
                'label'       => 'Férias & Licenças',
                'extra_price' => null,
                'description' => 'Gestão de férias e licenças dos colaboradores.',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 'c1b2c3d4-0003-0003-0003-000000000003',
                'key'         => 'payroll',
                'label'       => 'Folha de Pagamento',
                'extra_price' => 149.00,
                'description' => 'Processamento completo da folha de pagamento.',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 'c1b2c3d4-0004-0004-0004-000000000004',
                'key'         => 'benefits',
                'label'       => 'Benefícios',
                'extra_price' => null,
                'description' => 'Gestão de benefícios dos colaboradores.',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 'c1b2c3d4-0005-0005-0005-000000000005',
                'key'         => 'documents_signatures',
                'label'       => 'Documentos e Assinaturas',
                'extra_price' => null,
                'description' => 'Gestão e assinatura de documentos.',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 'c1b2c3d4-0006-0006-0006-000000000006',
                'key'         => 'reports_audit',
                'label'       => 'Relatórios e Auditoria',
                'extra_price' => 119.00,
                'description' => 'Relatórios completos e trilha de auditoria.',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ];

        DB::table('modules')->insertOrIgnore($modules);
    }
}
