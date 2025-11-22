@extends('layouts.app')

@section('title', 'Atendimentos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        <i class="fas fa-file-medical"></i> Atendimentos
    </h1>
    <a href="{{ route('attendances.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Novo Atendimento
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('attendances.index') }}">
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
                        <option value="concluido" {{ request('status') == 'concluido' ? 'selected' : '' }}>Concluído</option>
                        <option value="interrompido" {{ request('status') == 'interrompido' ? 'selected' : '' }}>Interrompido</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status Pagamento</label>
                    <select name="status_pagamento" class="form-select">
                        <option value="">Todos</option>
                        <option value="pendente" {{ request('status_pagamento') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="pago" {{ request('status_pagamento') == 'pago' ? 'selected' : '' }}>Pago</option>
                        <option value="pago_via_sessao" {{ request('status_pagamento') == 'pago_via_sessao' ? 'selected' : '' }}>Pago via Sessão</option>
                    </select>
                </div>
                <div class="col-md-12 d-flex">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('attendances.index') }}" class="btn btn-secondary">
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
        @if($attendances->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-file-medical fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum atendimento encontrado</h5>
                <a href="{{ route('attendances.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Criar Primeiro Atendimento
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
                            <th>Valor</th>
                            <th>Status</th>
                            <th>Pagamento</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendances as $attendance)
                            <tr>
                                <td>{{ $attendance->id }}</td>
                                <td>
                                    <strong>{{ $attendance->data_realizacao->format('d/m/Y') }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $attendance->data_realizacao->format('H:i') }}</small>
                                </td>
                                <td>{{ $attendance->patient->nome }}</td>
                                <td>{{ $attendance->professional->nome }}</td>
                                <td>
                                    @if($attendance->valor)
                                        R$ {{ number_format($attendance->valor, 2, ',', '.') }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($attendance->status == 'concluido')
                                        <span class="badge bg-success">Concluído</span>
                                    @else
                                        <span class="badge bg-warning">Interrompido</span>
                                    @endif
                                </td>
                                <td>
                                    @if($attendance->status_pagamento == 'pago')
                                        <span class="badge bg-success">Pago</span>
                                    @elseif($attendance->status_pagamento == 'pago_via_sessao')
                                        <span class="badge bg-info">Pago via Sessão</span>
                                    @elseif($attendance->status_pagamento == 'estornado')
                                        <span class="badge bg-danger">Estornado</span>
                                    @else
                                        <span class="badge bg-warning">Pendente</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('attendances.show', $attendance) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('attendances.edit', $attendance) }}" 
                                           class="btn btn-sm btn-warning" 
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        @if($attendance->isPendente())
                                            <a href="{{ route('payments.create', ['attendance_id' => $attendance->id]) }}" 
                                               class="btn btn-sm btn-success" 
                                               title="Registrar Pagamento">
                                                <i class="fas fa-dollar-sign"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Estatísticas -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body text-center">
                <h5>Atendimentos Concluídos</h5>
                <h2>{{ $attendances->where('status', 'concluido')->count() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body text-center">
                <h5>Pagamentos Pendentes</h5>
                <h2>{{ $attendances->where('status_pagamento', 'pendente')->count() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body text-center">
                <h5>Pagos via Sessão</h5>
                <h2>{{ $attendances->where('status_pagamento', 'pago_via_sessao')->count() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body text-center">
                <h5>Total Hoje</h5>
                <h2>
                    {{ $attendances->where('data_realizacao', '>=', today())->count() }}
                </h2>
            </div>
        </div>
    </div>
</div>
@endsection