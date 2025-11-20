@extends('professional.layouts.app')

@section('title', 'Dashboard Profissional')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Estatísticas principais -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card text-center">
            <div class="icon bg-primary mx-auto mb-3">
                <i class="fas fa-users"></i>
            </div>
            <h3 class="text-primary fw-bold">{{ $totalPatients }}</h3>
            <p class="text-muted mb-0">Total de Pacientes</p>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card text-center">
            <div class="icon bg-success mx-auto mb-3">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <h3 class="text-success fw-bold">{{ $totalSessions }}</h3>
            <p class="text-muted mb-0">Total de Sessões</p>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card text-center">
            <div class="icon bg-info mx-auto mb-3">
                <i class="fas fa-play-circle"></i>
            </div>
            <h3 class="text-info fw-bold">{{ $activeSessions }}</h3>
            <p class="text-muted mb-0">Sessões Ativas</p>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card text-center">
            <div class="icon bg-warning mx-auto mb-3">
                <i class="fas fa-calendar-day"></i>
            </div>
            <h3 class="text-warning fw-bold">{{ $appointmentsToday }}</h3>
            <p class="text-muted mb-0">Agendamentos Hoje</p>
        </div>
    </div>
</div>

<div class="row">
    <!-- Próximos agendamentos -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Próximos Agendamentos
                </h5>
            </div>
            <div class="card-body">
                @if($upcomingAppointments->isEmpty())
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Nenhum agendamento próximo.</p>
                        <a href="{{ route('professional.appointments.index') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Ver Todos os Agendamentos
                        </a>
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
                                    <th>Ações</th>
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
                                        <td>
                                            <strong>{{ $appointment->patient->nome }}</strong>
                                        </td>
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
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('professional.appointments.show', $appointment) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Ver Detalhes">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($appointment->status == 'agendado')
                                                    <form method="POST" 
                                                          action="{{ route('professional.appointments.confirm', $appointment) }}" 
                                                          class="d-inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-success" 
                                                                title="Confirmar">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('professional.appointments.index') }}" class="btn btn-outline-primary">
                            Ver Todos os Agendamentos
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Sessões próximas do fim -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Sessões Perto de Terminar
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
                                    <span class="badge bg-warning">Faltam {{ $session->total_sessoes - $session->sessoes_realizadas }}</span>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('professional.sessions.show', $session) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        Ver Detalhes
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Card de Receita Mensal -->
        <div class="card mt-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Receita Mensal
                </h5>
            </div>
            <div class="card-body text-center">
                <h3 class="text-success fw-bold">R$ {{ number_format($monthlyRevenue, 2, ',', '.') }}</h3>
                <p class="text-muted mb-0">Mês atual</p>
                @if($pendingPayments > 0)
                    <div class="mt-3">
                        <small class="text-warning">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            {{ $pendingPayments }} pagamento(s) pendente(s)
                        </small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Ações Rápidas -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Ações Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('professional.appointments.today') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-calendar-day me-2"></i>
                            Agendamentos de Hoje
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('professional.appointments.week') }}" class="btn btn-outline-success w-100">
                            <i class="fas fa-calendar-week me-2"></i>
                            Agenda da Semana
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('professional.sessions.create') }}" class="btn btn-outline-info w-100">
                            <i class="fas fa-plus me-2"></i>
                            Nova Sessão
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('professional.patients.create') }}" class="btn btn-outline-warning w-100">
                            <i class="fas fa-user-plus me-2"></i>
                            Novo Paciente
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection