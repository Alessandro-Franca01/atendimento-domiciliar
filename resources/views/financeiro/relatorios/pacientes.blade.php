@extends('layouts.app')

@section('title', 'Relatório de Pacientes')

@section('content')
<h1 class="h4 mb-3">Pacientes Inadimplentes</h1>

<div class="card mb-4">
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead><tr><th>Paciente</th><th>Fatura</th><th>Vencimento</th><th>Valor</th></tr></thead>
            <tbody>
                @forelse($inadimplentes as $f)
                    <tr>
                        <td>{{ $f->paciente->nome }}</td>
                        <td>#{{ $f->id }}</td>
                        <td>{{ \Carbon\Carbon::parse($f->data_vencimento)->format('d/m/Y') }}</td>
                        <td>R$ {{ number_format($f->valor_total, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center">Nenhum paciente inadimplente.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
 </div>

<h2 class="h5 mb-3">Histórico de Pagamentos (Últimos 50)</h2>
<div class="card">
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead><tr><th>Data</th><th>Paciente</th><th>Valor</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($historicoPagamentos as $p)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($p->data_pagamento)->format('d/m/Y') }}</td>
                        <td>{{ $p->paciente->nome }}</td>
                        <td>R$ {{ number_format($p->valor, 2, ',', '.') }}</td>
                        <td>{{ ucfirst($p->status) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center">Sem pagamentos.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
 </div>
@endsection