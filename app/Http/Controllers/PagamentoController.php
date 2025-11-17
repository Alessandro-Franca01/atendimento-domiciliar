<?php

namespace App\Http\Controllers;

use App\Models\Atendimento;
use App\Models\Paciente;
use App\Models\Profissional;
use App\Models\Sessao;
use App\Models\Pagamento;
use App\Services\FinanceService;
use Illuminate\Http\Request;

class PagamentoController extends Controller
{
    public function index()
    {
        $pagamentos = Pagamento::with(['paciente', 'profissional', 'sessao', 'atendimento'])->latest()->paginate(15);
        return view('financeiro.pagamentos.index', compact('pagamentos'));
    }

    public function create()
    {
        $pacientes = Paciente::where('status', 'ativo')->get();
        $profissionals = Profissional::where('status', 'ativo')->get();
        $sessoes = Sessao::where('status', 'ativo')->get();
        $atendimentos = Atendimento::all();
        return view('financeiro.pagamentos.create', compact('pacientes', 'profissionals', 'sessoes', 'atendimentos'));
    }

    public function store(Request $request, FinanceService $finance)
    {
        $data = $request->validate([
            'paciente_id' => ['required','exists:pacientes,id'],
            'profissional_id' => ['required','exists:profissionals,id'],
            'metodo_pagamento' => ['required','in:pix,dinheiro,cartao,transferencia'],
            'valor' => ['required','numeric','min:0.01'],
            'data_pagamento' => ['nullable','date'],
            'observacoes' => ['nullable','string'],
            'sessao_id' => ['nullable','exists:sessoes,id'],
            'atendimento_id' => ['nullable','exists:atendimentos,id'],
            'fatura_id' => ['nullable','exists:faturas,id'],
        ]);

        if (!empty($data['sessao_id'])) {
            $sessao = Sessao::findOrFail($data['sessao_id']);
            $pagamento = $finance->pagarSessao($sessao, $data);
        } elseif (!empty($data['atendimento_id'])) {
            $atendimento = Atendimento::findOrFail($data['atendimento_id']);
            $pagamento = $finance->pagarAtendimento($atendimento, $data);
        } else {
            return back()->with('error', 'Informe sessão ou atendimento.');
        }

        return redirect()->route('pagamentos.index')->with('success', 'Pagamento registrado com sucesso.');
    }

    public function estornar(Pagamento $pagamento, FinanceService $finance)
    {
        $finance->estornarPagamento($pagamento);
        return redirect()->route('pagamentos.index')->with('success', 'Pagamento estornado.');
    }
}