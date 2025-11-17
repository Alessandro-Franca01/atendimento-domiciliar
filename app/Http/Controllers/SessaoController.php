<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Profissional;
use App\Models\Sessao;
use App\Models\SessaoHorario;
use App\Services\AgendamentoService;
use Illuminate\Http\Request;

class SessaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sessoes = Sessao::with(['paciente', 'profissional'])
            ->orderBy('data_inicio', 'desc')
            ->paginate(10);

        return view('sessoes.index', compact('sessoes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pacientes = Paciente::where('status', 'ativo')->orderBy('nome')->get();
        $profissionals = Profissional::where('status', 'ativo')->orderBy('nome')->get();

        return view('sessoes.create', compact('pacientes', 'profissionals'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'profissional_id' => 'required|exists:profissionals,id',
            'descricao' => 'required|string|max:255',
            'total_sessoes' => 'required|integer|min:1',
            'valor_por_sessao' => 'nullable|numeric|min:0',
            'desconto_percentual' => 'nullable|numeric|min:0|max:100',
            'data_inicio' => 'required|date',
            'data_fim_prevista' => 'nullable|date|after_or_equal:data_inicio',
        ]);

        $dados = $request->all();
        if (!empty($dados['valor_por_sessao'])) {
            $bruto = (float) $dados['total_sessoes'] * (float) $dados['valor_por_sessao'];
            if (!empty($dados['desconto_valor']) && (float) $dados['desconto_valor'] > 0) {
                $dados['valor_total'] = max(0, round($bruto - (float) $dados['desconto_valor'], 2));
            } else {
                $desconto = (float) ($dados['desconto_percentual'] ?? 0);
                $fator = max(0, min(100, $desconto));
                $dados['valor_total'] = round($bruto * (1 - ($fator / 100)), 2);
            }
        }

        $sessao = Sessao::create($dados);

        return redirect()->route('sessoes.show', $sessao)
            ->with('success', 'Sessão criada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sessao $sessao)
    {
        $sessao->load(['paciente', 'profissional', 'sessaoHorarios.endereco', 'agendamentos']);
        return view('sessoes.show', compact('sessao'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sessao $sessao)
    {
        if ($sessao->status === 'concluido') {
            return redirect()->route('sessoes.show', $sessao)
                ->with('error', 'Não é possível editar uma sessão concluída.');
        }

        $pacientes = Paciente::where('status', 'ativo')->orderBy('nome')->get();
        $profissionals = Profissional::where('status', 'ativo')->orderBy('nome')->get();

        return view('sessoes.edit', compact('sessao', 'pacientes', 'profissionals'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sessao $sessao)
    {
        if ($sessao->status === 'concluido') {
            return redirect()->route('sessoes.show', $sessao)
                ->with('error', 'Não é possível editar uma sessão concluída.');
        }

        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'profissional_id' => 'required|exists:profissionals,id',
            'descricao' => 'required|string|max:255',
            'total_sessoes' => 'required|integer|min:1',
            'valor_por_sessao' => 'nullable|numeric|min:0',
            'desconto_percentual' => 'nullable|numeric|min:0|max:100',
            'data_inicio' => 'required|date',
            'data_fim_prevista' => 'nullable|date|after_or_equal:data_inicio',
            'status' => 'required|in:ativo,concluido,suspenso',
        ]);

        $dados = $request->all();
        if (!empty($dados['valor_por_sessao'])) {
            $bruto = (float) $dados['total_sessoes'] * (float) $dados['valor_por_sessao'];
            if (!empty($dados['desconto_valor']) && (float) $dados['desconto_valor'] > 0) {
                $dados['valor_total'] = max(0, round($bruto - (float) $dados['desconto_valor'], 2));
            } else {
                $desconto = (float) ($dados['desconto_percentual'] ?? 0);
                $fator = max(0, min(100, $desconto));
                $dados['valor_total'] = round($bruto * (1 - ($fator / 100)), 2);
            }
        }

        $sessao->update($dados);

        return redirect()->route('sessoes.show', $sessao)
            ->with('success', 'Sessão atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sessao $sessao)
    {
        if ($sessao->agendamentos()->exists()) {
            return redirect()->route('sessoes.show', $sessao)
                ->with('error', 'Não é possível excluir sessão com agendamentos.');
        }

        $sessao->delete();

        return redirect()->route('sessoes.index')
            ->with('success', 'Sessão excluída com sucesso.');
    }

    /**
     * Gera agendamentos automáticos para uma sessão
     */
    public function gerarAgendamentos(Sessao $sessao, Request $request)
    {
        if ($sessao->status !== 'ativo') {
            return redirect()->route('sessoes.show', $sessao)
                ->with('error', 'Só é possível gerar agendamentos para sessões ativas.');
        }

        $request->validate([
            'dias' => 'required|integer|min:1|max:365',
        ]);

        $agendamentoService = new AgendamentoService();
        $agendamentosCriados = $agendamentoService->gerarAgendamentosAutomaticos($request->dias);

        return redirect()->route('sessoes.show', $sessao)
            ->with('success', "{$agendamentosCriados} agendamentos automáticos foram criados.");
    }
}
