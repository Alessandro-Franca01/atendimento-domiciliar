@extends('layouts.app')

@section('title', 'Relatório Geral')

@section('content')
<h1 class="h4 mb-3">Relatório Geral</h1>

<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="card"><div class="card-body"><strong>Total ganho no mês:</strong><br>R$ {{ number_format($totalMes, 2, ',', '.') }}</div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-body"><strong>Ticket médio por paciente:</strong><br>R$ {{ number_format($ticketMedio, 2, ',', '.') }}</div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-body"><strong>Sessões com receita:</strong><br>{{ $lucroPorSessao->count() }}</div></div></div>
 </div>

<div class="card">
    <div class="card-header">Receita por Sessão</div>
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead><tr><th>Sessão</th><th>Total</th><th>Pago</th><th>Saldo</th></tr></thead>
            <tbody>
                @forelse($lucroPorSessao as $s)
                    <tr>
                        <td>#{{ $s->id }}</td>
                        <td>R$ {{ number_format($s->valor_total ?? 0, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($s->valor_pago ?? 0, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format(max(0, ($s->valor_total ?? 0) - ($s->valor_pago ?? 0)), 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center">Sem dados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
 </div>
@endsection