@extends('layouts.app')

@section('title', 'Sessão #' . $therapySession->id)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4">
        <i class="fas fa-clipboard-list"></i> 
        Sessão #{{ $therapySession->id }} - {{ $therapySession->descricao }}
    </h1>
    <div>
        <a href="{{ route('therapy-sessions.edit', $therapySession) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Editar
        </a>
        <a href="{{ route('therapy-sessions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<div class="row">
    <!-- Informações Principais -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informações da Sessão</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <strong>Paciente:</strong>
                        <p class="mb-0">{{ $therapySession->patient->nome }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Profissional:</strong>
                        <p class="mb-0">{{ $therapySession->professional->nome }}</p>
                    </div>
                    <div class="col-md-12">
                        <strong>Descrição:</strong>
                        <p class="mb-0">{{ $therapySession->descricao }}</p>
                    </div>
                    <div class="col-md-4">
                        <strong>Total de Sessões:</strong>
                        <p class="mb-0">{{ $therapySession->total_sessoes }}</p>
                    </div>
                    <div class="col-md-4">
                        <strong>Sessões Realizadas:</strong>
                        <p class="mb-0">
                            <span class="badge bg-primary">{{ $therapySession->sessoes_realizadas }}</span>
                        </p>
                    </div>
                    <div class="col-md-4">
                        <strong>Sessões Restantes:</strong>
                        <p class="mb-0">
                            <span class="badge bg-info">{{ $therapySession->sessoes_restantes }}</span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <strong>Data de Início:</strong>
                        <p class="mb-0">{{ $therapySession->data_inicio->format('d/m/Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Previsão de Término:</strong>
                        <p class="mb-0">
                            {{ $therapySession->data_fim_prevista ? $therapySession->data_fim_prevista->format('d/m/Y') : '—' }}
                        </p>
                    </div>
                    <div class="col-md-12">
                        <strong>Status:</strong>
                        <p class="mb-0">
                            @if($therapySession->status == 'ativo')
                                <span class="badge bg-success">Ativo</span>
                            @elseif($therapySession->status == 'concluido')
                                <span class="badge bg-secondary">Concluído</span>
                            @else
                                <span class="badge bg-warning">Suspenso</span>
                            @endif
                        </p>
                    </div>
                </div>
                
                <!-- Barra de Progresso -->
                <div class="mt-4">
                    <strong>Progresso:</strong>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar" role="progressbar" 
                             style="width: {{ $therapySession->percentual_concluido }}%;" 
                             aria-valuenow="{{ $therapySession->percentual_concluido }}" 
                             aria-valuemin="0" aria-valuemax="100">
                            {{ number_format($therapySession->percentual_concluido, 1) }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Horários Fixos -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-clock"></i> Horários Fixos</h5>
            </div>
            <div class="card-body">
                @if($therapySession->sessionSchedules->isEmpty())
                    <p class="text-muted mb-0">Nenhum horário fixo cadastrado.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Dia da Semana</th>
                                    <th>Horário</th>
                                    <th>Duração</th>
                                    <th>Endereço</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($therapySession->sessionSchedules as $schedule)
                                    <tr>
                                        <td>{{ $schedule->dia_semana_nome }}</td>
                                        <td>{{ \Carbon\Carbon::parse($schedule->hora)->format('H:i') }}</td>
                                        <td>{{ $schedule->duracao_minutos }} min</td>
                                        <td>
                                            {{ $schedule->address->logradouro }}, {{ $schedule->address->numero }}
                                            <br>
                                            <small class="text-muted">{{ $schedule->address->bairro }}</small>
                                        </td>
                                        <td>
                                            @if($schedule->ativo)
                                                <span class="badge bg-success">Ativo</span>
                                            @else
                                                <span class="badge bg-secondary">Inativo</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Agendamentos -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Agendamentos</h5>
            </div>
            <div class="card-body">
                @if($therapySession->appointments->isEmpty())
                    <p class="text-muted mb-0">Nenhum agendamento cadastrado.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Data/Hora</th>
                                    <th>Endereço</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($therapySession->appointments->sortBy('data_hora_inicio') as $appointment)
                                    <tr>
                                        <td>
                                            {{ $appointment->data_hora_inicio->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            {{ $appointment->address->logradouro }}, {{ $appointment->address->numero }}
                                        </td>
                                        <td>
                                            @if($appointment->status == 'confirmado')
                                                <span class="badge bg-success">Confirmado</span>
                                            @elseif($appointment->status == 'agendado')
                                                <span class="badge bg-primary">Agendado</span>
                                            @elseif($appointment->status == 'concluido')
                                                <span class="badge bg-secondary">Concluído</span>
                                            @elseif($appointment->status == 'cancelado')
                                                <span class="badge bg-danger">Cancelado</span>
                                            @else
                                                <span class="badge bg-warning">{{ ucfirst($appointment->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Informações Financeiras -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-dollar-sign"></i> Valores</h5>
            </div>
            <div class="card-body">
                @if($therapySession->valor_total)
                    <div class="mb-3">
                        <strong>Valor por Sessão:</strong>
                        <p class="mb-0">R$ {{ number_format($therapySession->valor_por_sessao ?? 0, 2, ',', '.') }}</p>
                    </div>
                    
                    @if(($therapySession->desconto_valor ?? 0) > 0)
                        <div class="mb-3">
                            <strong>Desconto Fixo:</strong>
                            <p class="mb-0 text-info">
                                - R$ {{ number_format($therapySession->desconto_valor, 2, ',', '.') }}
                            </p>
                        </div>
                    @elseif(($therapySession->desconto_percentual ?? 0) > 0)
                        <div class="mb-3">
                            <strong>Desconto:</strong>
                            <p class="mb-0 text-info">
                                {{ number_format($therapySession->desconto_percentual, 2, ',', '.') }}%
                            </p>
                        </div>
                    @endif
                    
                    <hr>
                    
                    <div class="mb-3">
                        <strong>Valor Total:</strong>
                        <h4 class="text-primary mb-0">
                            R$ {{ number_format($therapySession->valor_total, 2, ',', '.') }}
                        </h4>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Valor Pago:</strong>
                        <h4 class="text-success mb-0">
                            R$ {{ number_format($therapySession->valor_pago ?? 0, 2, ',', '.') }}
                        </h4>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Saldo:</strong>
                        <h4 class="{{ $therapySession->saldo_pagamento > 0 ? 'text-danger' : 'text-success' }} mb-0">
                            R$ {{ number_format($therapySession->saldo_pagamento, 2, ',', '.') }}
                        </h4>
                    </div>
                    
                    @if($therapySession->saldo_pagamento > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Sessão possui saldo pendente
                        </div>
                    @else
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            Sessão totalmente paga
                        </div>
                    @endif
                @else
                    <p class="text-muted mb-0">Sessão sem valores definidos</p>
                @endif
            </div>
        </div>
        
        <!-- Ações -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-cog"></i> Ações</h5>
            </div>
            <div class="card-body">
                @if($therapySession->status == 'ativo' && !$therapySession->isCompleta())
                    <form method="POST" action="{{ route('therapy-sessions.generate-appointments', $therapySession) }}" class="mb-2">
                        @csrf
                        <input type="hidden" name="dias" value="30">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-calendar-plus"></i> Gerar Agendamentos (30 dias)
                        </button>
                    </form>
                @endif
                
                @if($therapySession->valor_total && $therapySession->saldo_pagamento > 0)
                    <a href="{{ route('payments.create', ['therapy_session_id' => $therapySession->id]) }}" 
                       class="btn btn-success w-100 mb-2">
                        <i class="fas fa-money-bill"></i> Registrar Pagamento
                    </a>
                @endif
                
                <a href="{{ route('therapy-sessions.edit', $therapySession) }}" class="btn btn-warning w-100 mb-2">
                    <i class="fas fa-edit"></i> Editar Sessão
                </a>
                
                @if(!$therapySession->appointments->count())
                    <form method="POST" action="{{ route('therapy-sessions.destroy', $therapySession) }}" 
                          onsubmit="return confirm('Tem certeza que deseja excluir esta sessão?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash"></i> Excluir Sessão
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection