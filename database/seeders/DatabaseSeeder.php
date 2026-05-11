<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CompanySeeder::class,
            UserSeeder::class,
            TrackingSeeder::class,
            PlanTierSeeder::class,
            ModuleSeeder::class,
            EmployeeRangeMultiplierSeeder::class,
            AddOnSeeder::class,
            PlanTierAddOnSeeder::class,
            PlanTierModuleSeeder::class,
        ]);
    }
}
