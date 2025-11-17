@extends('layouts.app')

@section('title', 'Fatura #' . $fatura->id)

@section('content')
<h1 class="h4 mb-3">Fatura #{{ $fatura->id }} — {{ $fatura->paciente->nome }}</h1>

<div class="row g-3 mb-3">
    <div class="col-md-3"><strong>Valor Total:</strong> R$ {{ number_format($fatura->valor_total, 2, ',', '.') }}</div>
    <div class="col-md-3"><strong>Emissão:</strong> {{ \Carbon\Carbon::parse($fatura->data_emissao)->format('d/m/Y') }}</div>
    <div class="col-md-3"><strong>Vencimento:</strong> {{ \Carbon\Carbon::parse($fatura->data_vencimento)->format('d/m/Y') }}</div>
    <div class="col-md-3"><strong>Status:</strong> {{ ucfirst($fatura->status) }}</div>
</div>

<div class="card mb-4">
    <div class="card-header">Itens</div>
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Qtd</th>
                    <th>Unitário</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fatura->itens as $item)
                    <tr>
                        <td>{{ $item->descricao }}</td>
                        <td>{{ $item->quantidade }}</td>
                        <td>R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($item->valor_total, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center">Nenhum item.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
 </div>

<div class="card">
    <div class="card-header">Pagamentos</div>
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Método</th>
                    <th>Valor</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fatura->pagamentos as $p)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($p->data_pagamento)->format('d/m/Y') }}</td>
                        <td>{{ strtoupper($p->metodo_pagamento) }}</td>
                        <td>R$ {{ number_format($p->valor, 2, ',', '.') }}</td>
                        <td>{{ ucfirst($p->status) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center">Nenhum pagamento.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
 </div>
@endsection