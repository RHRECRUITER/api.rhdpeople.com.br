<?php

namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\Ponto;
use App\Models\Ferias;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Estatísticas
        $totalColaboradores = Colaborador::count();
        $colaboradoresAtivos = Colaborador::where('status', 'ativo')->count();
        
        $mesAtual = now()->month;
        $anoAtual = now()->year;
        
        $pontosHoje = Ponto::whereDate('data', today())->count();
        $feriasEmAndamento = Ferias::where('status', 'aprovado')
            ->whereDate('data_inicio', '<=', today())
            ->whereDate('data_fim', '>=', today())
            ->count();

        // Dados para gráficos
        $colaboradoresPorDepartamento = Colaborador::selectRaw('departamento, COUNT(*) as total')
            ->groupBy('departamento')
            ->get();

        return view('dashboard', compact(
            'user',
            'totalColaboradores',
            'colaboradoresAtivos',
            'pontosHoje',
            'feriasEmAndamento',
            'colaboradoresPorDepartamento'
        ));
    }
}
