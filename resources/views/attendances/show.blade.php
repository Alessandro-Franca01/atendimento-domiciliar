@extends('layouts.app')

@section('title', 'Atendimento #' . $attendance->id)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        <i class="fas fa-file-medical"></i> Atendimento #{{ $attendance->id }}
    </h1>
    <div>
        <a href="{{ route('attendances.edit', $attendance) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Editar
        </a>
        <a href="{{ route('attendances.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<div class="row">
    <!-- Informações Principais -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informações do Atendimento</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <strong>Paciente:</strong>
                        <p class="mb-0">{{ $attendance->patient->nome }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Profissional:</strong>
                        <p class="mb-0">{{ $attendance->professional->nome }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Data de Realização:</strong>
                        <p class="mb-0">{{ $attendance->data_realizacao->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <p class="mb-0">
                            @if($attendance->status == 'concluido')
                                <span class="badge bg-success">Concluído</span>
                            @else
                                <span class="badge bg-warning">Interrompido</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Procedimento e Evolução -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-notes-medical"></i> Registro Clínico</h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="text-primary">Procedimento Realizado:</h6>
                    <div class="p-3 bg-light rounded">
                        {{ $attendance->procedimento_realizado }}
                    </div>
                </div>
                
                <div class="mb-4">
                    <h6 class="text-primary">Evolução do Paciente:</h6>
                    <div class="p-3 bg-light rounded">
                        {{ $attendance->evolucao }}
                    </div>
                </div>
                
                @if($attendance->assinatura_paciente)
                    <div>
                        <h6 class="text-primary">Assinatura do Paciente:</h6>
                        <div class="p-3 bg-light rounded">
                            <i class="fas fa-signature"></i> {{ $attendance->assinatura_paciente }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Agendamento Relacionado -->
        @if($attendance->appointment)
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Agendamento Relacionado</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <strong>Agendamento:</strong>
                            <p class="mb-0">#{{ $attendance->appointment->id }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Data do Agendamento:</strong>
                            <p class="mb-0">{{ $attendance->appointment->data_hora_inicio->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-12">
                            <strong>Endereço:</strong>
                            <p class="mb-0">
                                {{ $attendance->appointment->address->logradouro }}, 
                                {{ $attendance->appointment->address->numero }}
                                <br>
                                {{ $attendance->appointment->address->bairro }}, 
                                {{ $attendance->appointment->address->cidade }}
                            </p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('appointments.show', $attendance->appointment) }}" 
                           class="btn btn-sm btn-outline-success">
                            <i class="fas fa-eye"></i> Ver Agendamento
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
    
    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Informações Financeiras -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-dollar-sign"></i> Informações Financeiras</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Valor do Atendimento:</strong>
                    <h4 class="text-primary mb-0">
                        @if($attendance->valor)
                            R$ {{ number_format($attendance->valor, 2, ',', '.') }}
                        @else
                            <span class="text-muted">Não informado</span>
                        @endif
                    </h4>
                </div>
                
                <div class="mb-3">
                    <strong>Status de Pagamento:</strong>
                    <p class="mb-0">
                        @if($attendance->status_pagamento == 'pago')
                            <span class="badge bg-success">Pago</span>
                        @elseif($attendance->status_pagamento == 'pago_via_sessao')
                            <span class="badge bg-info">Pago via Sessão</span>
                        @elseif($attendance->status_pagamento == 'estornado')
                            <span class="badge bg-danger">Estornado</span>
                        @else
                            <span class="badge bg-warning">Pendente</span>
                        @endif
                    </p>
                </div>
                
                @if($attendance->isPendente() && $attendance->valor)
                    <a href="{{ route('payments.create', ['attendance_id' => $attendance->id, 'valor' => $attendance->valor]) }}" 
                       class="btn btn-success w-100">
                        <i class="fas fa-dollar-sign"></i> Registrar Pagamento
                    </a>
                @endif
            </div>
        </div>
        
        <!-- Ações -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-cog"></i> Ações</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('attendances.edit', $attendance) }}" class="btn btn-warning w-100 mb-2">
                    <i class="fas fa-edit"></i> Editar Atendimento
                </a>
                
                <button type="button" class="btn btn-secondary w-100 mb-2" onclick="window.print()">
                    <i class="fas fa-print"></i> Imprimir
                </button>
                
                @if($attendance->appointment)
                    <a href="{{ route('appointments.show', $attendance->appointment) }}" 
                       class="btn btn-outline-primary w-100">
                        <i class="fas fa-calendar-alt"></i> Ver Agendamento
                    </a>
                @endif
            </div>
        </div>
        
        <!-- Informações do Sistema -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clock"></i> Informações</h5>
            </div>
            <div class="card-body">
                <small class="text-muted">
                    <i class="fas fa-calendar-plus"></i> 
                    Registrado em: {{ $attendance->created_at->format('d/m/Y H:i') }}
                </small>
                <br>
                <small class="text-muted">
                    <i class="fas fa-edit"></i> 
                    Última atualização: {{ $attendance->updated_at->format('d/m/Y H:i') }}
                </small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    .btn, .card-header, nav, footer {
        display: none !important;
    }
    .card {
        border: 1px solid #000 !important;
        page-break-inside: avoid;
    }
}
</style>
@endpush