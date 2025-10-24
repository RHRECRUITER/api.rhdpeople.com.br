<?php

namespace Database\Seeders;

use App\Models\Plano;
use Illuminate\Database\Seeder;

class PlanoSeeder extends Seeder
{
    public function run(): void
    {
        Plano::create([
            'nome' => 'Básico',
            'preco_mensal' => 99.90,
            'max_usuarios' => 2,
            'descricao' => 'Plano básico para pequenas empresas',
            'ativo' => true,
        ]);

        Plano::create([
            'nome' => 'Intermediário',
            'preco_mensal' => 299.90,
            'max_usuarios' => 10,
            'descricao' => 'Plano intermediário para médias empresas',
            'ativo' => true,
        ]);

        Plano::create([
            'nome' => 'Empresarial',
            'preco_mensal' => 599.90,
            'max_usuarios' => null, // ilimitado
            'descricao' => 'Plano empresarial com usuários ilimitados',
            'ativo' => true,
        ]);
    }
}
