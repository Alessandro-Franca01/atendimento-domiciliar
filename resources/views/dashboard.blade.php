@extends('layouts.app')

@section('title', 'Dashboard - Agendamento Domiciliar')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Total de Pacientes</h5>
                        <h2 class="mb-0">{{ $totalPatients }}</h2>
                    </div>
                    <i class="fas fa-users fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Total de Sessões</h5>
                        <h2 class="mb-0">{{ $totalSessions }}</h2>
                    </div>
                    <i class="fas fa-calendar-check fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-info mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Sessões Ativas</h5>
                        <h2 class="mb-0">{{ $activeSessions }}</h2>
                    </div>
                    <i class="fas fa-play-circle fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Agendamentos Hoje</h5>
                        <h2 class="mb-0">{{ $appointmentsToday }}</h2>
                    </div>
                    <i class="fas fa-calendar-day fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-alt"></i> Próximos Agendamentos
                </h5>
            </div>
            <div class="card-body">
                @if($upcomingAppointments->isEmpty())
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Nenhum agendamento próximo.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Data/Hora</th>
                                    <th>Paciente</th>
                                    <th>Endereço</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($upcomingAppointments as $appointment)
                                    <tr>
                                        <td>
                                            <strong>{{ $appointment->data_hora_inicio->format('d/m/Y') }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $appointment->data_hora_inicio->format('H:i') }}</small>
                                        </td>
                                        <td>{{ $appointment->patient->nome }}</td>
                                        <td>
                                            {{ $appointment->address->logradouro }}, {{ $appointment->address->numero }}
                                            <br>
                                            <small class="text-muted">{{ $appointment->address->bairro }}</small>
                                        </td>
                                        <td>
                                            @if($appointment->status == 'confirmado')
                                                <span class="badge bg-success">Confirmado</span>
                                            @elseif($appointment->status == 'agendado')
                                                <span class="badge bg-primary">Agendado</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($appointment->status) }}</span>
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
    
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-exclamation-triangle"></i> Sessões Perto de Terminar
                </h5>
            </div>
            <div class="card-body">
                @if($sessionsNearEnd->isEmpty())
                    <div class="text-center py-3">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <p class="text-muted mb-0">Nenhuma sessão próxima do fim.</p>
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($sessionsNearEnd as $session)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $session->patient->nome }}</h6>
                                        <p class="mb-1 small">{{ $session->descricao }}</p>
                                        <small class="text-muted">
                                            {{ $session->sessoes_realizadas }}/{{ $session->total_sessoes }} sessões
                                        </small>
                                    </div>
                                    <span class="badge bg-warning">
                                        Faltam {{ $session->sessoes_restantes }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection