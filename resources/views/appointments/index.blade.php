@extends('layouts.app')

@section('title', 'Agendamentos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        <i class="fas fa-calendar-alt"></i> Agendamentos
    </h1>
    <a href="{{ route('appointments.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Novo Agendamento
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('appointments.index') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Data Início</label>
                    <input type="date" name="date_start" class="form-control" value="{{ request('date_start') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Data Fim</label>
                    <input type="date" name="date_end" class="form-control" value="{{ request('date_end') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="agendado" {{ request('status') == 'agendado' ? 'selected' : '' }}>Agendado</option>
                        <option value="confirmado" {{ request('status') == 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                        <option value="cancelado" {{ request('status') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        <option value="concluido" {{ request('status') == 'concluido' ? 'selected' : '' }}>Concluído</option>
                        <option value="faltou" {{ request('status') == 'faltou' ? 'selected' : '' }}>Faltou</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('appointments.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Limpar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Listagem -->
<div class="card">
    <div class="card-body">
        @if($appointments->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum agendamento encontrado</h5>
                <a href="{{ route('appointments.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Criar Primeiro Agendamento
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Data/Hora</th>
                            <th>Paciente</th>
                            <th>Profissional</th>
                            <th>Endereço</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointments as $appointment)
                            <tr>
                                <td>{{ $appointment->id }}</td>
                                <td>
                                    <strong>{{ $appointment->data_hora_inicio->format('d/m/Y') }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ $appointment->data_hora_inicio->format('H:i') }} - 
                                        {{ $appointment->data_hora_fim->format('H:i') }}
                                    </small>
                                </td>
                                <td>{{ $appointment->patient->nome }}</td>
                                <td>{{ $appointment->professional->nome }}</td>
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
                                    @elseif($appointment->status == 'concluido')
                                        <span class="badge bg-secondary">Concluído</span>
                                    @elseif($appointment->status == 'cancelado')
                                        <span class="badge bg-danger">Cancelado</span>
                                    @elseif($appointment->status == 'faltou')
                                        <span class="badge bg-warning">Faltou</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('appointments.show', $appointment) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($appointment->podeSerCancelado())
                                            <a href="{{ route('appointments.edit', $appointment) }}" 
                                               class="btn btn-sm btn-warning" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        
                                        @if($appointment->status == 'agendado')
                                            <form method="POST" 
                                                  action="{{ route('appointments.confirm', $appointment) }}" 
                                                  class="d-inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-sm btn-success" 
                                                        title="Confirmar">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($appointment->podeSerCancelado())
                                            <form method="POST" 
                                                  action="{{ route('appointments.cancel', $appointment) }}" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Tem certeza que deseja cancelar este agendamento?')">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-sm btn-danger" 
                                                        title="Cancelar">
                                                    <i class="fas fa-times"></i>
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
            
            <div class="d-flex justify-content-center">
                {{ $appointments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection