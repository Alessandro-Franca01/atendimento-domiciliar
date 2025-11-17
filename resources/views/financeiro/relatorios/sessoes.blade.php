@extends('layouts.app')

@section('title', 'Relatório de Sessões')

@section('content')
<h1 class="h4 mb-3">Relatório de Sessões</h1>

<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="card"><div class="card-body"><strong>Pagas:</strong> {{ $pagas }}</div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-body"><strong>Não Pagas:</strong> {{ $naoPagas }}</div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-body"><strong>Atendimentos Pendentes:</strong> {{ $pendentesAtendimentos }}</div></div></div>
 </div>

<div class="card">
    <div class="card-header">Sessões</div>
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead>
                <tr><th>#</th><th>Descrição</th><th>Total</th><th>Pago</th><th>Saldo</th></tr>
            </thead>
            <tbody>
                @forelse($sessoes as $s)
                    <tr>
                        <td>{{ $s->id }}</td>
                        <td>{{ $s->descricao }}</td>
                        <td>R$ {{ number_format($s->valor_total ?? 0, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($s->valor_pago ?? 0, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format(($s->saldo_pagamento ?? 0), 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">Sem sessões.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
 </div>
@endsection