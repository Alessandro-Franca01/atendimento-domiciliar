<?php

namespace App\Http\Controllers;

use App\Models\Atendimento;
use App\Models\Fatura;
use App\Models\Paciente;
use App\Models\Pagamento;
use App\Models\Sessao;
use Carbon\Carbon;

class RelatorioFinanceiroController extends Controller
{
    public function fluxoCaixa()
    {
        $hoje = Carbon::now();
        $inicioMes = $hoje->copy()->startOfMonth();
        $fimMes = $hoje->copy()->endOfMonth();

        $diario = Pagamento::where('status', 'pago')
            ->whereBetween('data_pagamento', [$inicioMes, $fimMes])
            ->selectRaw('date(data_pagamento) as dia, sum(valor) as total')
            ->groupBy('dia')
            ->orderBy('dia')
            ->get();

        $totalMes = $diario->sum('total');

        $previsaoRecebiveis = Fatura::where('status', 'aberta')
            ->whereBetween('data_vencimento', [$inicioMes, $fimMes])
            ->sum('valor_total');

        return view('financeiro.relatorios.fluxo_caixa', compact('diario', 'totalMes', 'previsaoRecebiveis'));
    }

    public function relatorioSessoes()
    {
        $sessoes = Sessao::withCount(['agendamentos'])->get();
        $pagas = $sessoes->filter(fn($s) => ($s->valor_total ?? 0) > 0 && abs(($s->valor_total) - ($s->valor_pago ?? 0)) < 0.00001)->count();
        $naoPagas = $sessoes->count() - $pagas;
        $pendentesAtendimentos = Atendimento::where('status_pagamento', 'pendente')->count();
        return view('financeiro.relatorios.sessoes', compact('sessoes', 'pagas', 'naoPagas', 'pendentesAtendimentos'));
    }

    public function relatorioPacientes()
    {
        $inadimplentes = Fatura::where('status', 'vencida')->with('paciente')->get();
        $historicoPagamentos = Pagamento::with('paciente')->latest()->limit(50)->get();
        return view('financeiro.relatorios.pacientes', compact('inadimplentes', 'historicoPagamentos'));
    }

    public function relatorioGeral()
    {
        $hoje = Carbon::now();
        $inicioMes = $hoje->copy()->startOfMonth();
        $fimMes = $hoje->copy()->endOfMonth();

        $pagamentosMes = Pagamento::where('status', 'pago')
            ->whereBetween('data_pagamento', [$inicioMes, $fimMes])
            ->get();
        $totalMes = $pagamentosMes->sum('valor');
        $pacientesNoMes = $pagamentosMes->pluck('paciente_id')->unique()->count();
        $ticketMedio = $pacientesNoMes > 0 ? $totalMes / $pacientesNoMes : 0;

        $lucroPorSessao = Sessao::whereNotNull('valor_total')
            ->selectRaw('id, valor_total, valor_pago')
            ->get();

        return view('financeiro.relatorios.geral', compact('totalMes', 'ticketMedio', 'lucroPorSessao'));
    }
}