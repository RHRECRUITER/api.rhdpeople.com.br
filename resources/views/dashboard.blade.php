@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Bem-vindo -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Bem-vindo, {{ $user->name }}!</h1>
        <p class="text-gray-600">Visão geral do sistema de gestão de RH</p>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Colaboradores</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalColaboradores }}</p>
                </div>
                <div class="h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Colaboradores Ativos</p>
                    <p class="text-3xl font-bold text-green-600">{{ $colaboradoresAtivos }}</p>
                </div>
                <div class="h-12 w-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-check text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Pontos Hoje</p>
                    <p class="text-3xl font-bold text-primary">{{ $pontosHoje }}</p>
                </div>
                <div class="h-12 w-12 bg-teal-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-primary text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Férias em Andamento</p>
                    <p class="text-3xl font-bold text-orange-600">{{ $feriasEmAndamento }}</p>
                </div>
                <div class="h-12 w-12 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-umbrella-beach text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Colaboradores por Departamento</h3>
            <canvas id="deptChart" height="250"></canvas>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Presença Mensal</h3>
            <canvas id="presenceChart" height="250"></canvas>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Gráfico de Departamentos
const deptCtx = document.getElementById('deptChart');
new Chart(deptCtx, {
    type: 'doughnut',
    data: {
        labels: @json($colaboradoresPorDepartamento->pluck('departamento')),
        datasets: [{
            data: @json($colaboradoresPorDepartamento->pluck('total')),
            backgroundColor: ['#009E9A', '#00CFC9', '#FFC107', '#FF5722', '#9C27B0']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Gráfico de Presença
const presenceCtx = document.getElementById('presenceChart');
new Chart(presenceCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
        datasets: [{
            label: 'Presença (%)',
            data: [95, 93, 96, 94, 97, 95],
            borderColor: '#009E9A',
            backgroundColor: 'rgba(0, 158, 154, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
</script>
@endsection
