@extends('layouts.app')

@section('title', 'Colaboradores')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Colaboradores</h1>
        <a href="{{ route('colaboradores.create') }}" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark">
            <i class="fas fa-plus mr-2"></i>Novo Colaborador
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cargo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Departamento</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($colaboradores as $colaborador)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $colaborador->nome }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $colaborador->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $colaborador->cargo }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $colaborador->departamento }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($colaborador->status == 'ativo') bg-green-100 text-green-800
                                @elseif($colaborador->status == 'afastado') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($colaborador->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('colaboradores.edit', $colaborador) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('colaboradores.destroy', $colaborador) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Tem certeza?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Nenhum colaborador cadastrado</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $colaboradores->links() }}
    </div>
</div>
@endsection
