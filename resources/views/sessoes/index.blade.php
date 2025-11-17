@extends('layouts.app')

@section('title', 'Sessões')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4">Sessões</h1>
    <a href="{{ route('sessoes.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nova Sessão</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Paciente</th>
                    <th>Profissional</th>
                    <th>Descrição</th>
                    <th>Total</th>
                    <th>Vlr/Sessão</th>
                    <th>Desc (%)</th>
                    <th>Desc (R$)</th>
                    <th>Pago</th>
                    <th>Saldo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sessoes as $s)
                    <tr>
                        <td>{{ $s->id }}</td>
                        <td>{{ $s->paciente->nome }}</td>
                        <td>{{ $s->profissional->nome }}</td>
                        <td>{{ $s->descricao }}</td>
                        <td>
                            R$ {{ number_format($s->valor_total ?? 0, 2, ',', '.') }}
                            @if(($s->desconto_valor ?? 0) > 0)
                                <span class="badge bg-info ms-1">Desconto fixo</span>
                            @elseif(($s->desconto_percentual ?? 0) > 0)
                                <span class="badge bg-secondary ms-1">Desconto %</span>
                            @endif
                        </td>
                        <td>R$ {{ number_format($s->valor_por_sessao ?? 0, 2, ',', '.') }}</td>
                        <td>{{ number_format($s->desconto_percentual ?? 0, 2, ',', '.') }}%</td>
                        <td>R$ {{ number_format($s->desconto_valor ?? 0, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($s->valor_pago ?? 0, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format(($s->saldo_pagamento ?? 0), 2, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('sessoes.show', $s) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('sessoes.edit', $s) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center">Nenhuma sessão cadastrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $sessoes->links() }}</div>
 </div>
@endsection
