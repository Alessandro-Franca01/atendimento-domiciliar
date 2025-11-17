@extends('layouts.app')

@section('title', 'Fluxo de Caixa')

@section('content')
<h1 class="h4 mb-3">Fluxo de Caixa do Mês</h1>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card"><div class="card-body"><strong>Total do Mês:</strong><br>R$ {{ number_format($totalMes, 2, ',', '.') }}</div></div>
    </div>
    <div class="col-md-4">
        <div class="card"><div class="card-body"><strong>Previsão de Recebíveis:</strong><br>R$ {{ number_format($previsaoRecebiveis, 2, ',', '.') }}</div></div>
    </div>
    <div class="col-md-4">
        <div class="card"><div class="card-body"><strong>Dias com Entradas:</strong><br>{{ $diario->count() }}</div></div>
    </div>
 </div>

<div class="card">
    <div class="card-header">Entradas por Dia</div>
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead><tr><th>Dia</th><th>Total</th></tr></thead>
            <tbody>
                @forelse($diario as $d)
                    <tr><td>{{ \Carbon\Carbon::parse($d->dia)->format('d/m') }}</td><td>R$ {{ number_format($d->total, 2, ',', '.') }}</td></tr>
                @empty
                    <tr><td colspan="2" class="text-center">Sem entradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
 </div>
@endsection