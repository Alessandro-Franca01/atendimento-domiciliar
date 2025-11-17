@extends('layouts.app')

@section('title', 'Sessão #' . $sessao->id)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4">Sessão #{{ $sessao->id }}</h1>
    <div>
        <a href="{{ route('sessoes.edit', $sessao) }}" class="btn btn-secondary"><i class="fas fa-edit"></i> Editar</a>
    </div>
 </div>

<div class="row g-3 mb-3">
    <div class="col-md-3"><strong>Paciente:</strong> {{ $sessao->paciente->nome }}</div>
    <div class="col-md-3"><strong>Profissional:</strong> {{ $sessao->profissional->nome }}</div>
    <div class="col-md-6"><strong>Descrição:</strong> {{ $sessao->descricao }}</div>
    <div class="col-md-3"><strong>Total de Sessões:</strong> {{ $sessao->total_sessoes }}</div>
    <div class="col-md-3"><strong>Realizadas:</strong> {{ $sessao->sessoes_realizadas }}</div>
    <div class="col-md-3"><strong>Status:</strong> {{ ucfirst($sessao->status) }}</div>
    <div class="col-md-3">
        <strong>Valor do Pacote:</strong> R$ {{ number_format($sessao->valor_total ?? 0, 2, ',', '.') }}
        @if(($sessao->desconto_valor ?? 0) > 0)
            <span class="badge bg-info ms-1">Desconto fixo</span>
        @elseif(($sessao->desconto_percentual ?? 0) > 0)
            <span class="badge bg-secondary ms-1">Desconto %</span>
        @endif
    </div>
    <div class="col-md-3"><strong>Valor por Sessão:</strong> R$ {{ number_format($sessao->valor_por_sessao ?? 0, 2, ',', '.') }}</div>
    <div class="col-md-3"><strong>Desconto:</strong> {{ number_format($sessao->desconto_percentual ?? 0, 2, ',', '.') }}%</div>
    <div class="col-md-3"><strong>Desconto Fixo:</strong> R$ {{ number_format($sessao->desconto_valor ?? 0, 2, ',', '.') }}</div>
    <div class="col-md-3"><strong>Pago:</strong> R$ {{ number_format($sessao->valor_pago ?? 0, 2, ',', '.') }}</div>
    <div class="col-md-3"><strong>Saldo:</strong> R$ {{ number_format(($sessao->saldo_pagamento ?? 0), 2, ',', '.') }}</div>
 </div>

<div class="card">
    <div class="card-header">Horários Fixos</div>
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead><tr><th>Dia</th><th>Hora</th><th>Duração</th><th>Endereço</th></tr></thead>
            <tbody>
                @forelse($sessao->sessaoHorarios as $h)
                    <tr>
                        <td>{{ $h->dia_da_semana }}</td>
                        <td>{{ $h->hora }}</td>
                        <td>{{ $h->duracao_minutos }} min</td>
                        <td>{{ $h->endereco?->descricao ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center">Sem horários fixos.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
 </div>
@endsection