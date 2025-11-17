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
                        <h2 class="mb-0">{{ $totalPacientes }}</h2>
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
                        <h2 class="mb-0">{{ $totalSessoes }}</h2>
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
                        <h2 class="mb-0">{{ $sessoesAtivas }}</h2>
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
                        <h2 class="mb-0">{{ $agendamentosHoje }}</h2>
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
                @if($proximosAgendamentos->isEmpty())
                    <p class="text-muted">Nenhum agendamento próximo.</p>
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
                                @foreach($proximosAgendamentos as $agendamento)
                                    <tr>
                                        <td>{{ $agendamento->data_hora_inicio->format('d/m/Y H:i') }}</td>
                                        <td>{{ $agendamento->paciente->nome }}</td>
                                        <td>
                                            {{ $agendamento->endereco->logradouro }}, {{ $agendamento->endereco->numero }}
                                            <br><small class="text-muted">{{ $agendamento->endereco->bairro }}</small>
                                        </td>
                                        <td>
                                            @if($agendamento->status == 'confirmado')
                                                <span class="badge bg-success">Confirmado</span>
                                            @elseif($agendamento->status == 'agendado')
                                                <span class="badge bg-primary">Agendado</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($agendamento->status) }}</span>
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
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-exclamation-triangle"></i> Sessões Perto de Terminar
                </h5>
            </div>
            <div class="card-body">
                @if($sessoesPertoDeTerminar->isEmpty())
                    <p class="text-muted">Nenhuma sessão próxima do fim.</p>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($sessoesPertoDeTerminar as $sessao)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $sessao->paciente->nome }}</h6>
                                        <p class="mb-1">{{ $sessao->descricao }}</p>
                                        <small class="text-muted">
                                            {{ $sessao->sessoes_realizadas }}/{{ $sessao->total_sessoes }} sessões
                                        </small>
                                    </div>
                                    <span class="badge bg-warning">Faltam {{ $sessao->total_sessoes - $sessao->sessoes_realizadas }}</span>
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