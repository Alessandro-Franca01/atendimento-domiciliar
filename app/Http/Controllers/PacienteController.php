<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pacientes = Paciente::with('enderecos')
            ->orderBy('nome')
            ->paginate(10);

        return view('pacientes.index', compact('pacientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pacientes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:20',
            'documento' => 'required|string|max:20|unique:pacientes',
            'observacoes' => 'nullable|string',
            'status' => 'required|in:ativo,inativo',
        ]);

        Paciente::create($request->all());

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Paciente $paciente)
    {
        $paciente->load(['enderecos', 'sessoes', 'agendamentos']);
        return view('pacientes.show', compact('paciente'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Paciente $paciente)
    {
        return view('pacientes.edit', compact('paciente'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Paciente $paciente)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:20',
            'documento' => 'required|string|max:20|unique:pacientes,documento,' . $paciente->id,
            'observacoes' => 'nullable|string',
            'status' => 'required|in:ativo,inativo',
        ]);

        $paciente->update($request->all());

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Paciente $paciente)
    {
        if ($paciente->sessoes()->exists() || $paciente->agendamentos()->exists()) {
            return redirect()->route('pacientes.index')
                ->with('error', 'Não é possível excluir paciente com sessões ou agendamentos.');
        }

        $paciente->delete();

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente excluído com sucesso.');
    }
}
