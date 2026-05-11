<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeRangeMultiplierSeeder extends Seeder
{
    public function run(): void
    {
        $ranges = [
            // ─── Start ───────────────────────────────────────────
            [
                'id'               => 'b1b2c3d4-0001-0001-0001-000000000001',
                'plan_tier_id'     => 'a1b2c3d4-0001-0001-0001-000000000001',
                'range_label'      => 'Até 25 colaboradores',
                'min_employees'    => 1,
                'max_employees'    => 25,
                'price_multiplier' => 1.00,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],

            // ─── Growth ───────────────────────────────────────────
            [
                'id'               => 'b1b2c3d4-0002-0002-0002-000000000001',
                'plan_tier_id'     => 'a1b2c3d4-0002-0002-0002-000000000002',
                'range_label'      => 'Até 25 colaboradores',
                'min_employees'    => 1,
                'max_employees'    => 25,
                'price_multiplier' => 1.00,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'id'               => 'b1b2c3d4-0002-0002-0002-000000000002',
                'plan_tier_id'     => 'a1b2c3d4-0002-0002-0002-000000000002',
                'range_label'      => '26 a 100 colaboradores',
                'min_employees'    => 26,
                'max_employees'    => 100,
                'price_multiplier' => 1.80,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],

            // ─── Pro ─────────────────────────────────────────────
            [
                'id'               => 'b1b2c3d4-0003-0003-0003-000000000001',
                'plan_tier_id'     => 'a1b2c3d4-0003-0003-0003-000000000003',
                'range_label'      => 'Até 25 colaboradores',
                'min_employees'    => 1,
                'max_employees'    => 25,
                'price_multiplier' => 1.00,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'id'               => 'b1b2c3d4-0003-0003-0003-000000000002',
                'plan_tier_id'     => 'a1b2c3d4-0003-0003-0003-000000000003',
                'range_label'      => '26 a 100 colaboradores',
                'min_employees'    => 26,
                'max_employees'    => 100,
                'price_multiplier' => 1.80,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'id'               => 'b1b2c3d4-0003-0003-0003-000000000003',
                'plan_tier_id'     => 'a1b2c3d4-0003-0003-0003-000000000003',
                'range_label'      => '101 a 300 colaboradores',
                'min_employees'    => 101,
                'max_employees'    => 300,
                'price_multiplier' => 2.50,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],

            // ─── Enterprise ───────────────────────────────────────
            [
                'id'               => 'b1b2c3d4-0004-0004-0004-000000000001',
                'plan_tier_id'     => 'a1b2c3d4-0004-0004-0004-000000000004',
                'range_label'      => 'Mais de 300',
                'min_employees'    => 301,
                'max_employees'    => null,
                'price_multiplier' => 3.50,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ];

        DB::table('employee_range_multipliers')->insertOrIgnore($ranges);
    }
}
