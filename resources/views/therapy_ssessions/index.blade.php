@extends('layouts.app')

@section('title', 'Sessões de Terapia')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4">Sessões de Terapia</h1>
    <a href="{{ route('therapy-sessions.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nova Sessão
    </a>
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
                    <th>Sessões</th>
                    <th>Valor Total</th>
                    <th>Pago</th>
                    <th>Saldo</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sessions as $session)
                    <tr>
                        <td>{{ $session->id }}</td>
                        <td>{{ $session->patient->nome }}</td>
                        <td>{{ $session->professional->nome }}</td>
                        <td>{{ $session->descricao }}</td>
                        <td>
                            <span class="badge bg-primary">
                                {{ $session->sessoes_realizadas }}/{{ $session->total_sessoes }}
                            </span>
                        </td>
                        <td>
                            R$ {{ number_format($session->valor_total ?? 0, 2, ',', '.') }}
                            @if(($session->desconto_valor ?? 0) > 0)
                                <span class="badge bg-info ms-1">Desc. fixo</span>
                            @elseif(($session->desconto_percentual ?? 0) > 0)
                                <span class="badge bg-secondary ms-1">{{ $session->desconto_percentual }}%</span>
                            @endif
                        </td>
                        <td>R$ {{ number_format($session->valor_pago ?? 0, 2, ',', '.') }}</td>
                        <td>
                            <span class="badge {{ $session->saldo_pagamento > 0 ? 'bg-warning' : 'bg-success' }}">
                                R$ {{ number_format($session->saldo_pagamento ?? 0, 2, ',', '.') }}
                            </span>
                        </td>
                        <td>
                            @if($session->status == 'ativo')
                                <span class="badge bg-success">Ativo</span>
                            @elseif($session->status == 'concluido')
                                <span class="badge bg-secondary">Concluído</span>
                            @else
                                <span class="badge bg-warning">Suspenso</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('therapy-sessions.show', $session) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('therapy-sessions.edit', $session) }}" 
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">Nenhuma sessão cadastrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $sessions->links() }}
    </div>
</div>
@endsection