@extends('layouts.app')

@section('title', 'Faturas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        <i class="fas fa-file-invoice"></i> Faturas
    </h1>
    <a href="{{ route('invoices.monthly.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Gerar Fatura Mensal
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('invoices.index') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Mês/Ano</label>
                    <input type="month" name="month" class="form-control" value="{{ request('month') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="aberta" {{ request('status') == 'aberta' ? 'selected' : '' }}>Aberta</option>
                        <option value="paga" {{ request('status') == 'paga' ? 'selected' : '' }}>Paga</option>
                        <option value="vencida" {{ request('status') == 'vencida' ? 'selected' : '' }}>Vencida</option>
                        <option value="cancelada" {{ request('status') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select">
                        <option value="">Todos</option>
                        <option value="mensalidade" {{ request('tipo') == 'mensalidade' ? 'selected' : '' }}>Mensalidade</option>
                        <option value="sessao_completa" {{ request('tipo') == 'sessao_completa' ? 'selected' : '' }}>Sessão Completa</option>
                        <option value="atendimento_avulso" {{ request('tipo') == 'atendimento_avulso' ? 'selected' : '' }}>Atendimento Avulso</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
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
                <h5>Faturas Pagas</h5>
                <h3>R$ {{ number_format($invoices->where('status', 'paga')->sum('valor_total'), 2, ',', '.') }}</h3>
                <small>{{ $invoices->where('status', 'paga')->count() }} faturas</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body text-center">
                <h5>Abertas</h5>
                <h3>R$ {{ number_format($invoices->where('status', 'aberta')->sum('valor_total'), 2, ',', '.') }}</h3>
                <small>{{ $invoices->where('status', 'aberta')->count() }} faturas</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger">
            <div class="card-body text-center">
                <h5>Vencidas</h5>
                <h3>R$ {{ number_format($invoices->where('status', 'vencida')->sum('valor_total'), 2, ',', '.') }}</h3>
                <small>{{ $invoices->where('status', 'vencida')->count() }} faturas</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body text-center">
                <h5>Total</h5>
                <h3>R$ {{ number_format($invoices->sum('valor_total'), 2, ',', '.') }}</h3>
                <small>{{ $invoices->count() }} faturas</small>
            </div>
        </div>
    </div>
</div>

<!-- Listagem -->
<div class="card">
    <div class="card-body">
        @if($invoices->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhuma fatura encontrada</h5>
                <a href="{{ route('invoices.monthly.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Gerar Primeira Fatura
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Paciente</th>
                            <th>Tipo</th>
                            <th>Emissão</th>
                            <th>Vencimento</th>
                            <th>Valor Total</th>
                            <th>Valor Pago</th>
                            <th>Saldo</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                            <tr>
                                <td>{{ $invoice->id }}</td>
                                <td>{{ $invoice->patient->nome }}</td>
                                <td>
                                    @if($invoice->tipo == 'mensalidade')
                                        <span class="badge bg-primary">Mensalidade</span>
                                    @elseif($invoice->tipo == 'sessao_completa')
                                        <span class="badge bg-info">Sessão Completa</span>
                                    @else
                                        <span class="badge bg-secondary">Atendimento Avulso</span>
                                    @endif
                                </td>
                                <td>{{ $invoice->data_emissao->format('d/m/Y') }}</td>
                                <td>
                                    {{ $invoice->data_vencimento->format('d/m/Y') }}
                                    @if($invoice->isVencida())
                                        <br><small class="text-danger">Vencida</small>
                                    @endif
                                </td>
                                <td>
                                    <strong>R$ {{ number_format($invoice->valor_total, 2, ',', '.') }}</strong>
                                </td>
                                <td>
                                    R$ {{ number_format($invoice->valor_pago, 2, ',', '.') }}
                                </td>
                                <td>
                                    <span class="badge {{ $invoice->saldo > 0 ? 'bg-warning' : 'bg-success' }}">
                                        R$ {{ number_format($invoice->saldo, 2, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    @if($invoice->status == 'paga')
                                        <span class="badge bg-success">Paga</span>
                                    @elseif($invoice->status == 'vencida')
                                        <span class="badge bg-danger">Vencida</span>
                                    @elseif($invoice->status == 'cancelada')
                                        <span class="badge bg-secondary">Cancelada</span>
                                    @else
                                        <span class="badge bg-warning">Aberta</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('invoices.show', $invoice) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($invoice->status == 'aberta' && $invoice->saldo > 0)
                                            <a href="{{ route('payments.create', ['invoice_id' => $invoice->id]) }}" 
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
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</div>
@endsection