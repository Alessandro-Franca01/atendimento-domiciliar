<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Atendimento;
use App\Models\Agendamento;
use App\Models\Paciente;
use App\Models\Profissional;

class AtendimentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $atendimentos = Atendimento::with(['paciente','profissional','agendamento'])->latest()->paginate(15);
        return view('atendimentos.index', compact('atendimentos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pacientes = Paciente::where('status','ativo')->get();
        $profissionals = Profissional::where('status','ativo')->get();
        $agendamentos = Agendamento::latest()->get();
        return view('atendimentos.create', compact('pacientes','profissionals','agendamentos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'paciente_id' => ['required','exists:pacientes,id'],
            'profissional_id' => ['required','exists:profissionals,id'],
            'agendamento_id' => ['nullable','exists:agendamentos,id'],
            'data_realizacao' => ['required','date'],
            'valor' => ['nullable','numeric','min:0'],
            'procedimento_realizado' => ['nullable','string'],
            'evolucao' => ['nullable','string'],
            'assinatura_paciente' => ['nullable','string'],
            'status' => ['required','in:concluido,interrompido'],
        ]);

        $atendimento = Atendimento::create($data);
        return redirect()->route('atendimentos.index')->with('success','Atendimento registrado.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $atendimento = Atendimento::with(['paciente','profissional','agendamento'])->findOrFail($id);
        return view('atendimentos.show', compact('atendimento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $atendimento = Atendimento::findOrFail($id);
        $pacientes = Paciente::where('status','ativo')->get();
        $profissionals = Profissional::where('status','ativo')->get();
        $agendamentos = Agendamento::latest()->get();
        return view('atendimentos.edit', compact('atendimento','pacientes','profissionals','agendamentos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'paciente_id' => ['required','exists:pacientes,id'],
            'profissional_id' => ['required','exists:profissionals,id'],
            'agendamento_id' => ['nullable','exists:agendamentos,id'],
            'data_realizacao' => ['required','date'],
            'valor' => ['nullable','numeric','min:0'],
            'procedimento_realizado' => ['nullable','string'],
            'evolucao' => ['nullable','string'],
            'assinatura_paciente' => ['nullable','string'],
            'status' => ['required','in:concluido,interrompido'],
        ]);

        $atendimento = Atendimento::findOrFail($id);
        $atendimento->update($data);
        return redirect()->route('atendimentos.show', $atendimento)->with('success','Atendimento atualizado.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $atendimento = Atendimento::findOrFail($id);
        $atendimento->delete();
        return redirect()->route('atendimentos.index')->with('success','Atendimento excluído.');
    }
}
