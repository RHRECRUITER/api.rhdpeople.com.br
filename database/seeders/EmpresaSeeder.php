<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\ConfiguracaoEmpresa;
use Illuminate\Database\Seeder;

class EmpresaSeeder extends Seeder
{
    public function run(): void
    {
        $empresa = Empresa::create([
            'plano_id' => 2, // Intermediário
            'nome' => 'Empresa Demo Ltda',
            'cnpj' => '12.345.678/0001-90',
            'email' => 'contato@empresademo.com',
            'telefone' => '(11) 98765-4321',
            'endereco' => 'Rua Demo, 123',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'cep' => '01234-567',
            'status' => 'ativa',
            'data_inicio' => now(),
        ]);

        // Criar configuração padrão
        ConfiguracaoEmpresa::create([
            'empresa_id' => $empresa->id,
            'cor_primaria' => '#009E9A',
            'cor_secundaria' => '#00CFC9',
            'layout' => 'padrao',
            'modulos_ativos' => ['dashboard', 'colaboradores', 'ponto', 'ferias', 'folha', 'beneficios', 'documentos', 'relatorios'],
        ]);
    }
}
