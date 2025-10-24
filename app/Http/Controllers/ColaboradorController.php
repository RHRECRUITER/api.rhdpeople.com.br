<?php

namespace App\Http\Controllers;

use App\Models\Colaborador;
use Illuminate\Http\Request;

class ColaboradorController extends Controller
{
    public function index()
    {
        $colaboradores = Colaborador::with('empresa')
            ->orderBy('nome')
            ->paginate(20);

        return view('colaboradores.index', compact('colaboradores'));
    }

    public function create()
    {
        return view('colaboradores.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:200',
            'cpf' => 'required|string|max:14|unique:colaboradores',
            'email' => 'required|email|max:100|unique:colaboradores',
            'telefone' => 'nullable|string|max:20',
            'cargo' => 'nullable|string|max:100',
            'departamento' => 'nullable|string|max:100',
            'salario' => 'nullable|numeric',
            'data_admissao' => 'nullable|date',
        ]);

        Colaborador::create($validated);

        return redirect()->route('colaboradores.index')
            ->with('success', 'Colaborador cadastrado com sucesso!');
    }

    public function show(Colaborador $colaborador)
    {
        return view('colaboradores.show', compact('colaborador'));
    }

    public function edit(Colaborador $colaborador)
    {
        return view('colaboradores.edit', compact('colaborador'));
    }

    public function update(Request $request, Colaborador $colaborador)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:200',
            'cpf' => 'required|string|max:14|unique:colaboradores,cpf,' . $colaborador->id,
            'email' => 'required|email|max:100|unique:colaboradores,email,' . $colaborador->id,
            'telefone' => 'nullable|string|max:20',
            'cargo' => 'nullable|string|max:100',
            'departamento' => 'nullable|string|max:100',
            'salario' => 'nullable|numeric',
            'data_admissao' => 'nullable|date',
            'status' => 'required|in:ativo,afastado,demitido',
        ]);

        $colaborador->update($validated);

        return redirect()->route('colaboradores.index')
            ->with('success', 'Colaborador atualizado com sucesso!');
    }

    public function destroy(Colaborador $colaborador)
    {
        $colaborador->delete();

        return redirect()->route('colaboradores.index')
            ->with('success', 'Colaborador removido com sucesso!');
    }
}
