@extends('layouts.app')

@section('title', 'Fatura #' . $invoice->id)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        <i class="fas fa-file-invoice"></i> Fatura #{{ $invoice->id }}
    </h1>
    <div>
        <button class="btn btn-secondary" onclick="window.print()">
            <i class="fas fa-print"></i> Imprimir
        </button>
        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<div class="row">
    <!-- Informações da Fatura -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informações da Fatura</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <strong>Paciente:</strong>
                        <p class="mb-0">{{ $invoice->patient->nome }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>CPF:</strong>
                        <p class="mb-0">{{ $invoice->patient->cpf }}</p>
                    </div>
                    <div class="col-md-4">
                        <strong>Data de Emissão:</strong>
                        <p class="mb-0">{{ $invoice->data_emissao->format('d/m/Y') }}</p>
                    </div>
                    <div class="col-md-4">
                        <strong>Data de Vencimento:</strong>
                        <p class="mb-0">
                            {{ $invoice->data_vencimento->format('d/m/Y') }}
                            @if($invoice->isVencida())
                                <br><span class="badge bg-danger">Vencida</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4">
                        <strong>Tipo:</strong>
                        <p class="mb-0">
                            @if($invoice->tipo == 'mensalidade')
                                <span class="badge bg-primary">Mensalidade</span>
                            @elseif($invoice->tipo == 'sessao_completa')
                                <span class="badge bg-info">Sessão Completa</span>
                            @else
                                <span class="badge bg-secondary">Atendimento Avulso</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-12">
                        <strong>Status:</strong>
                        <p class="mb-0">
                            @if($invoice->status == 'paga')
                                <span class="badge bg-success">Paga</span>
                            @elseif($invoice->status == 'vencida')
                                <span class="badge bg-danger">Vencida</span>
                            @elseif($invoice->status == 'cancelada')
                                <span class="badge bg-secondary">Cancelada</span>
                            @else
                                <span class="badge bg-warning">Aberta</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Itens da Fatura -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-list"></i> Itens da Fatura</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Descrição</th>
                                <th class="text-center">Qtd</th>
                                <th class="text-end">Valor Unit.</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $item)
                                <tr>
                                    <td>
                                        {{ $item->descricao }}
                                        @if($item->attendance_id)
                                            <br><small class="text-muted">Atendimento #{{ $item->attendance_id }}</small>
                                        @endif
                                        @if($item->therapy_session_id)
                                            <br><small class="text-muted">Sessão #{{ $item->therapy_session_id }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->quantidade }}</td>
                                    <td class="text-end">R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
                                    <td class="text-end">
                                        <strong>R$ {{ number_format($item->valor_total, 2, ',', '.') }}</strong>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td class="text-end">
                                    <h5 class="mb-0">R$ {{ number_format($invoice->valor_total, 2, ',', '.') }}</h5>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Pagamentos -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-dollar-sign"></i> Pagamentos</h5>
            </div>
            <div class="card-body">
                @if($invoice->payments->isEmpty())
                    <p class="text-muted mb-0">Nenhum pagamento registrado</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Método</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->payments as $payment)
                                    <tr>
                                        <td>{{ $payment->data_pagamento->format('d/m/Y') }}</td>
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
                                        <td>R$ {{ number_format($payment->valor, 2, ',', '.') }}</td>
                                        <td>
                                            @if($payment->status == 'pago')
                                                <span class="badge bg-success">Pago</span>
                                            @elseif($payment->status == 'estornado')
                                                <span class="badge bg-danger">Estornado</span>
                                            @else
                                                <span class="badge bg-warning">Pendente</span>
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
    
    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Resumo Financeiro -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-calculator"></i> Resumo</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Valor Total:</strong>
                    <h4 class="text-primary mb-0">
                        R$ {{ number_format($invoice->valor_total, 2, ',', '.') }}
                    </h4>
                </div>
                
                <div class="mb-3">
                    <strong>Valor Pago:</strong>
                    <h4 class="text-success mb-0">
                        R$ {{ number_format($invoice->valor_pago, 2, ',', '.') }}
                    </h4>
                </div>
                
                <div class="mb-3">
                    <strong>Saldo:</strong>
                    <h4 class="{{ $invoice->saldo > 0 ? 'text-danger' : 'text-success' }} mb-0">
                        R$ {{ number_format($invoice->saldo, 2, ',', '.') }}
                    </h4>
                </div>
                
                @if($invoice->saldo > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Fatura com saldo pendente
                    </div>
                @else
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        Fatura totalmente paga
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Ações -->
        @if($invoice->status == 'aberta' && $invoice->saldo > 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-cog"></i> Ações</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('payments.create', ['invoice_id' => $invoice->id, 'valor' => $invoice->saldo]) }}" 
                       class="btn btn-success w-100 mb-2">
                        <i class="fas fa-dollar-sign"></i> Registrar Pagamento
                    </a>
                    
                    <button class="btn btn-secondary w-100" onclick="window.print()">
                        <i class="fas fa-print"></i> Imprimir Fatura
                    </button>
                </div>
            </div>
        @endif
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