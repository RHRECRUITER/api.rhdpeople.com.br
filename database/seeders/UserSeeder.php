<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@atsdp.com',
            'password' => Hash::make('admin@2025'),
            'tipo' => 'super_admin',
            'ativo' => true,
        ]);

        // Admin da Empresa Demo
        User::create([
            'name' => 'Admin Demo',
            'email' => 'admin@empresademo.com',
            'password' => Hash::make('empresa@2025'),
            'empresa_id' => 1,
            'tipo' => 'admin_empresa',
            'ativo' => true,
        ]);

        // Usuário DP
        User::create([
            'name' => 'João Silva',
            'email' => 'joao@empresademo.com',
            'password' => Hash::make('dp@2025'),
            'empresa_id' => 1,
            'tipo' => 'user_dp',
            'ativo' => true,
        ]);
    }
}
