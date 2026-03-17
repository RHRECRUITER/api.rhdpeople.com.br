<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $rhLabs = Company::where('email', 'contato@rhdpeople.com.br')->first();
        $demoComp = Company::where('email', 'financeiro@demotech.com')->first();

        User::create([
            'id' => Str::uuid(),
            'tenant_id' => $rhLabs->id,
            'name' => 'Root Master',
            'email' => 'root@rhdpeople.com',
            'password' => Hash::make('admin@2025'),
            'type' => 'super_admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        User::create([
            'id' => Str::uuid(),
            'tenant_id' => $rhLabs->id,
            'name' => 'Suporte Técnico',
            'email' => 'suporte@rhdpeople.com',
            'password' => Hash::make('system@2025'),
            'type' => 'system_admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        User::create([
            'id' => Str::uuid(),
            'tenant_id' => $demoComp->id,
            'name' => 'Diretor Demo',
            'email' => 'diretor@empresademo.com',
            'password' => Hash::make('diretor@2025'),
            'type' => 'company_admin',
            'status' => 'active',
        ]);

        $users = [
            ['name' => 'Ana RH', 'email' => 'ana@empresademo.com'],
            ['name' => 'Bruno Recrutador', 'email' => 'bruno@empresademo.com'],
            ['name' => 'Carla DP', 'email' => 'carla@empresademo.com'],
        ];

        foreach ($users as $u) {
            User::create([
                'id' => Str::uuid(),
                'tenant_id' => $demoComp->id,
                'name' => $u['name'],
                'email' => $u['email'],
                'password' => Hash::make('user@2025'),
                'type' => 'company_user',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
        }
    }
}