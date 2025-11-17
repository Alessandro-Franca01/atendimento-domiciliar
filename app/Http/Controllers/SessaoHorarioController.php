<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use App\Models\Sessao;
use App\Models\SessaoHorario;
use Illuminate\Http\Request;

class SessaoHorarioController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Sessao $sessao)
    {
        if ($sessao->status === 'concluido') {
            return redirect()->route('sessoes.show', $sessao)
                ->with('error', 'Não é possível adicionar horários a uma sessão concluída.');
        }

        $request->validate([
            'dia_da_semana' => 'required|integer|between:1,7',
            'hora' => 'required|date_format:H:i',
            'duracao_minutos' => 'required|integer|min:30|max:240',
            'endereco_id' => 'required|exists:enderecos,id',
        ]);

        // Verificar se o endereço pertence ao paciente da sessão
        $endereco = Endereco::find($request->endereco_id);
        if ($endereco->paciente_id !== $sessao->paciente_id) {
            return redirect()->route('sessoes.show', $sessao)
                ->with('error', 'O endereço não pertence ao paciente da sessão.');
        }

        $sessao->sessaoHorarios()->create($request->all());

        return redirect()->route('sessoes.show', $sessao)
            ->with('success', 'Horário fixo adicionado com sucesso.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sessao $sessao, SessaoHorario $sessaoHorario)
    {
        if ($sessao->status === 'concluido') {
            return redirect()->route('sessoes.show', $sessao)
                ->with('error', 'Não é possível editar horários de uma sessão concluída.');
        }

        $request->validate([
            'dia_da_semana' => 'required|integer|between:1,7',
            'hora' => 'required|date_format:H:i',
            'duracao_minutos' => 'required|integer|min:30|max:240',
            'endereco_id' => 'required|exists:enderecos,id',
            'ativo' => 'boolean',
        ]);

        // Verificar se o endereço pertence ao paciente da sessão
        $endereco = Endereco::find($request->endereco_id);
        if ($endereco->paciente_id !== $sessao->paciente_id) {
            return redirect()->route('sessoes.show', $sessao)
                ->with('error', 'O endereço não pertence ao paciente da sessão.');
        }

        $sessaoHorario->update($request->all());

        return redirect()->route('sessoes.show', $sessao)
            ->with('success', 'Horário fixo atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sessao $sessao, SessaoHorario $sessaoHorario)
    {
        if ($sessao->status === 'concluido') {
            return redirect()->route('sessoes.show', $sessao)
                ->with('error', 'Não é possível excluir horários de uma sessão concluída.');
        }

        if ($sessaoHorario->agendamentos()->exists()) {
            return redirect()->route('sessoes.show', $sessao)
                ->with('error', 'Não é possível excluir horário com agendamentos.');
        }

        $sessaoHorario->delete();

        return redirect()->route('sessoes.show', $sessao)
            ->with('success', 'Horário fixo excluído com sucesso.');
    }
}
