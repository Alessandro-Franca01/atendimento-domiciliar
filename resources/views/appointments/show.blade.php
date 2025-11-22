@extends('layouts.app')

@section('title', 'Agendamento #' . $appointment->id)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        <i class="fas fa-calendar-alt"></i> Agendamento #{{ $appointment->id }}
    </h1>
    <div>
        @if($appointment->podeSerCancelado())
            <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
        @endif
        <a href="{{ route('appointments.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<div class="row">
    <!-- Informações do Agendamento -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informações do Agendamento</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <strong>Data:</strong>
                        <p class="mb-0">{{ $appointment->data_hora_inicio->format('d/m/Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Horário:</strong>
                        <p class="mb-0">
                            {{ $appointment->data_hora_inicio->format('H:i') }} - 
                            {{ $appointment->data_hora_fim->format('H:i') }}
                            <small class="text-muted">({{ $appointment->duracao_minutos }} min)</small>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <strong>Paciente:</strong>
                        <p class="mb-0">{{ $appointment->patient->nome }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Profissional:</strong>
                        <p class="mb-0">{{ $appointment->professional->nome }}</p>
                    </div>
                    <div class="col-md-12">
                        <strong>Endereço:</strong>
                        <p class="mb-0">
                            {{ $appointment->address->logradouro }}, {{ $appointment->address->numero }}
                            @if($appointment->address->complemento)
                                - {{ $appointment->address->complemento }}
                            @endif
                            <br>
                            {{ $appointment->address->bairro }}, {{ $appointment->address->cidade }}
                            <br>
                            CEP: {{ $appointment->address->cep }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <strong>Tipo:</strong>
                        <p class="mb-0">
                            @if($appointment->isFixo())
                                <span class="badge bg-info">Horário Fixo</span>
                            @else
                                <span class="badge bg-secondary">Avulso</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <p class="mb-0">
                            @if($appointment->status == 'confirmado')
                                <span class="badge bg-success">Confirmado</span>
                            @elseif($appointment->status == 'agendado')
                                <span class="badge bg-primary">Agendado</span>
                            @elseif($appointment->status == 'concluido')
                                <span class="badge bg-secondary">Concluído</span>
                            @elseif($appointment->status == 'cancelado')
                                <span class="badge bg-danger">Cancelado</span>
                            @elseif($appointment->status == 'faltou')
                                <span class="badge bg-warning">Faltou</span>
                            @endif
                        </p>
                    </div>
                    @if($appointment->observacoes)
                        <div class="col-md-12">
                            <strong>Observações:</strong>
                            <p class="mb-0">{{ $appointment->observacoes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Sessão de Terapia -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> Sessão de Terapia</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-12">
                        <strong>Descrição:</strong>
                        <p class="mb-0">{{ $appointment->therapySession->descricao }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Progresso:</strong>
                        <p class="mb-0">
                            {{ $appointment->therapySession->sessoes_realizadas }}/{{ $appointment->therapySession->total_sessoes }}
                            sessões
                        </p>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar" 
                                 style="width: {{ $appointment->therapySession->percentual_concluido }}%">
                                {{ number_format($appointment->therapySession->percentual_concluido, 1) }}%
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <strong>Status da Sessão:</strong>
                        <p class="mb-0">
                            @if($appointment->therapySession->status == 'ativo')
                                <span class="badge bg-success">Ativa</span>
                            @elseif($appointment->therapySession->status == 'concluido')
                                <span class="badge bg-secondary">Concluída</span>
                            @else
                                <span class="badge bg-warning">Suspensa</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('therapy-sessions.show', $appointment->therapySession) }}" 
                       class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye"></i> Ver Sessão Completa
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Atendimento -->
        @if($appointment->attendance)
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-file-medical"></i> Atendimento Realizado</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <strong>Data de Realização:</strong>
                            <p class="mb-0">{{ $appointment->attendance->data_realizacao->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong>
                            <p class="mb-0">
                                @if($appointment->attendance->status == 'concluido')
                                    <span class="badge bg-success">Concluído</span>
                                @else
                                    <span class="badge bg-warning">Interrompido</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('attendances.show', $appointment->attendance) }}" 
                           class="btn btn-sm btn-outline-success">
                            <i class="fas fa-eye"></i> Ver Atendimento Completo
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
    
    <!-- Ações -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-cog"></i> Ações</h5>
            </div>
            <div class="card-body">
                @if($appointment->status == 'agendado')
                    <form method="POST" action="{{ route('appointments.confirm', $appointment) }}" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check"></i> Confirmar Agendamento
                        </button>
                    </form>
                @endif
                
                @if($appointment->podeSerCancelado())
                    <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-warning w-100 mb-2">
                        <i class="fas fa-edit"></i> Editar Agendamento
                    </a>
                    
                    <form method="POST" 
                          action="{{ route('appointments.cancel', $appointment) }}" 
                          onsubmit="return confirm('Tem certeza que deseja cancelar este agendamento?')">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100 mb-2">
                            <i class="fas fa-times"></i> Cancelar Agendamento
                        </button>
                    </form>
                @endif
                
                @if($appointment->status == 'confirmado' && !$appointment->attendance)
                    <a href="{{ route('attendances.create', ['appointment_id' => $appointment->id]) }}" 
                       class="btn btn-primary w-100">
                        <i class="fas fa-plus"></i> Registrar Atendimento
                    </a>
                @endif
            </div>
        </div>
        
        <!-- Informações Adicionais -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clock"></i> Informações</h5>
            </div>
            <div class="card-body">
                <small class="text-muted">
                    <i class="fas fa-calendar-plus"></i> Criado em: {{ $appointment->created_at->format('d/m/Y H:i') }}
                </small>
                <br>
                <small class="text-muted">
                    <i class="fas fa-edit"></i> Atualizado em: {{ $appointment->updated_at->format('d/m/Y H:i') }}
                </small>
            </div>
        </div>
    </div>
</div>
@endsection