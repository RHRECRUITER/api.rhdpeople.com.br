<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanTierSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = [
            [
                'id' => 'a1b2c3d4-0001-0001-0001-000000000001',
                'name' => 'Start',
                'description' => 'Para pequenos times que precisam do básico',
                'base_price' => 199.00,
                'annual_discount_pct' => 15.00,
                'max_employees' => 25,
                'is_custom_pricing' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'a1b2c3d4-0002-0002-0002-000000000002',
                'name' => 'Growth',
                'description' => 'O mais completo para empresas em crescimento',
                'base_price' => 499.00,
                'annual_discount_pct' => 20.00,
                'max_employees' => 100,
                'is_custom_pricing' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'a1b2c3d4-0003-0003-0003-000000000003',
                'name' => 'Pro',
                'description' => 'DP completo com todos os recursos',
                'base_price' => 999.00,
                'annual_discount_pct' => 25.00,
                'max_employees' => 300,
                'is_custom_pricing' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'a1b2c3d4-0004-0004-0004-000000000004',
                'name' => 'Enterprise',
                'description' => 'Para grandes empresas com necessidades específicas',
                'base_price' => 0.00,
                'annual_discount_pct' => 0.00,
                'max_employees' => null,
                'is_custom_pricing' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('plan_tiers')->insertOrIgnore($tiers);
    }
}
