<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddOnSeeder extends Seeder
{
    public function run(): void
    {
        $addOns = [
            [
                'id'          => 'd1b2c3d4-0001-0001-0001-000000000001',
                'key'         => 'kyc_biometry',
                'label'       => 'KYC por batida (biometria)',
                'price'       => 49.00,
                'description' => 'Verificação biométrica por batida de ponto.',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 'd1b2c3d4-0002-0002-0002-000000000002',
                'key'         => 'gps_geofencing',
                'label'       => 'GPS obrigatório (geofencing)',
                'price'       => 39.00,
                'description' => 'Registro de ponto com GPS e geofencing obrigatório.',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 'd1b2c3d4-0003-0003-0003-000000000003',
                'key'         => 'digital_signature',
                'label'       => 'Assinatura digital jurídica',
                'price'       => 79.00,
                'description' => 'Assinatura digital com validade jurídica.',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 'd1b2c3d4-0004-0004-0004-000000000004',
                'key'         => 'advanced_export',
                'label'       => 'Exportação avançada',
                'price'       => 59.00,
                'description' => 'Exportação de dados em múltiplos formatos avançados.',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 'd1b2c3d4-0005-0005-0005-000000000005',
                'key'         => 'accounting_integrations',
                'label'       => 'Integrações contábeis',
                'price'       => 99.00,
                'description' => 'Integração com sistemas contábeis.',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 'd1b2c3d4-0006-0006-0006-000000000006',
                'key'         => 'advanced_audit_trail',
                'label'       => 'Trilha de auditoria avançada',
                'price'       => 69.00,
                'description' => 'Trilha de auditoria completa e detalhada.',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 'd1b2c3d4-0007-0007-0007-000000000007',
                'key'         => 'premium_support_sla',
                'label'       => 'Suporte premium + SLA',
                'price'       => 199.00,
                'description' => 'Suporte prioritário com SLA garantido.',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 'd1b2c3d4-0008-0008-0008-000000000008',
                'key'         => 'sso',
                'label'       => 'SSO (SAML/OAuth)',
                'price'       => 149.00,
                'description' => 'Single Sign-On via SAML ou OAuth.',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ];

        DB::table('add_ons')->insertOrIgnore($addOns);
    }
}
