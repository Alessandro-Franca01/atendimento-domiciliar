@extends('layouts.app')

@section('title', 'Pagamentos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        <i class="fas fa-dollar-sign"></i> Pagamentos
    </h1>
    <a href="{{ route('payments.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Novo Pagamento
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('payments.index') }}">
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
                    <label class="form-label">Método</label>
                    <select name="metodo_pagamento" class="form-select">
                        <option value="">Todos</option>
                        <option value="pix" {{ request('metodo_pagamento') == 'pix' ? 'selected' : '' }}>PIX</option>
                        <option value="dinheiro" {{ request('metodo_pagamento') == 'dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                        <option value="cartao" {{ request('metodo_pagamento') == 'cartao' ? 'selected' : '' }}>Cartão</option>
                        <option value="transferencia" {{ request('metodo_pagamento') == 'transferencia' ? 'selected' : '' }}>Transferência</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="pago" {{ request('status') == 'pago' ? 'selected' : '' }}>Pago</option>
                        <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="estornado" {{ request('status') == 'estornado' ? 'selected' : '' }}>Estornado</option>
                    </select>
                </div>
                <div class="col-md-12 d-flex">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('payments.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Limpar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Resumo -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body text-center">
                <h5>Total Recebido</h5>
                <h3>R$ {{ number_format($payments->where('status', 'pago')->sum('valor'), 2, ',', '.') }}</h3>
                <small>{{ $payments->where('status', 'pago')->count() }} pagamentos</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body text-center">
                <h5>Pendente</h5>
                <h3>R$ {{ number_format($payments->where('status', 'pendente')->sum('valor'), 2, ',', '.') }}</h3>
                <small>{{ $payments->where('status', 'pendente')->count() }} pendentes</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger">
            <div class="card-body text-center">
                <h5>Estornado</h5>
                <h3>R$ {{ number_format($payments->where('status', 'estornado')->sum('valor'), 2, ',', '.') }}</h3>
                <small>{{ $payments->where('status', 'estornado')->count() }} estornos</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body text-center">
                <h5>Hoje</h5>
                <h3>R$ {{ number_format($payments->where('data_pagamento', today())->where('status', 'pago')->sum('valor'), 2, ',', '.') }}</h3>
                <small>{{ $payments->where('data_pagamento', today())->count() }} pagamentos</small>
            </div>
        </div>
    </div>
</div>

<!-- Listagem -->
<div class="card">
    <div class="card-body">
        @if($payments->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-dollar-sign fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum pagamento encontrado</h5>
                <a href="{{ route('payments.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Criar Primeiro Pagamento
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Data</th>
                            <th>Paciente</th>
                            <th>Profissional</th>
                            <th>Método</th>
                            <th>Valor</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr>
                                <td>{{ $payment->id }}</td>
                                <td>{{ $payment->data_pagamento->format('d/m/Y') }}</td>
                                <td>{{ $payment->patient->nome }}</td>
                                <td>{{ $payment->professional->nome }}</td>
                                <td>
                                    @if($payment->metodo_pagamento == 'pix')
                                        <span class="badge bg-primary">PIX</span>
                                    @elseif($payment->metodo_pagamento == 'dinheiro')
                                        <span class="badge bg-success">Dinheiro</span>
                                    @elseif($payment->metodo_pagamento == 'cartao')
                                        <span class="badge bg-info">Cartão</span>
                                    @else
                                        <span class="badge bg-secondary">Transferência</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>R$ {{ number_format($payment->valor, 2, ',', '.') }}</strong>
                                </td>
                                <td>
                                    @if($payment->therapy_session_id)
                                        <span class="badge bg-purple">Sessão</span>
                                    @elseif($payment->attendance_id)
                                        <span class="badge bg-cyan">Atendimento</span>
                                    @else
                                        <span class="badge bg-secondary">Avulso</span>
                                    @endif
                                </td>
                                <td>
                                    @if($payment->status == 'pago')
                                        <span class="badge bg-success">Pago</span>
                                    @elseif($payment->status == 'estornado')
                                        <span class="badge bg-danger">Estornado</span>
                                    @else
                                        <span class="badge bg-warning">Pendente</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('payments.show', $payment) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($payment->status == 'pago')
                                            <a href="{{ route('payments.edit', $payment) }}" 
                                               class="btn btn-sm btn-warning" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
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
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-purple {
        background-color: #6f42c1 !important;
    }
    .bg-cyan {
        background-color: #17a2b8 !important;
    }
</style>
@endpush