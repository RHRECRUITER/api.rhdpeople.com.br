<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::create([
            'id' => Str::uuid(),
            'name' => 'RHDPeople Institucional',
            'document' => '12.345.678/0001-99',
            'email' => 'contato@rhdpeople.com.br',
            'phone' => '11999999999',
            'status' => 'active',
        ]);

        Company::create([
            'id' => Str::uuid(),
            'name' => 'Empresa Demo Secundária',
            'document' => '98.765.432/0001-00',
            'email' => 'financeiro@demotech.com',
            'phone' => '11888888888',
            'status' => 'active',
        ]);
    }
}
