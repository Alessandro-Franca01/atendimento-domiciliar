@extends('layouts.app')

@section('title', 'Relatório Geral')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        <i class="fas fa-chart-bar"></i> Relatório Financeiro Geral
    </h1>
    <button class="btn btn-secondary" onclick="window.print()">
        <i class="fas fa-print"></i> Imprimir
    </button>
</div>

<!-- Indicadores Principais -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-primary">
            <div class="card-body text-center">
                <h5>Faturamento do Mês</h5>
                <h2>R$ {{ number_format($totalMonth, 2, ',', '.') }}</h2>
                <small>{{ now()->format('F Y') }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body text-center">
                <h5>Ticket Médio</h5>
                <h2>R$ {{ number_format($averageTicket, 2, ',', '.') }}</h2>
                <small>Por paciente</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info">
            <div class="card-body text-center">
                <h5>Total de Sessões</h5>
                <h2>{{ $profitBySession->count() }}</h2>
                <small>Com valores definidos</small>
            </div>
        </div>
    </div>
</div>

<!-- Receita por Sessões -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> Análise de Sessões</h5>
    </div>
    <div class="card-body">
        @if($profitBySession->isEmpty())
            <p class="text-muted mb-0">Nenhuma sessão com valores definidos.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Valor Total</th>
                            <th>Valor Pago</th>
                            <th>Saldo</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($profitBySession as $session)
                            @php
                                $saldo = $session->valor_total - $session->valor_pago;
                            @endphp
                            <tr>
                                <td>{{ $session->id }}</td>
                                <td>R$ {{ number_format($session->valor_total, 2, ',', '.') }}</td>
                                <td>R$ {{ number_format($session->valor_pago, 2, ',', '.') }}</td>
                                <td>
                                    <span class="badge {{ $saldo > 0 ? 'bg-warning' : 'bg-success' }}">
                                        R$ {{ number_format($saldo, 2, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    @if($saldo <= 0)
                                        <i class="fas fa-check-circle text-success"></i> Paga
                                    @else
                                        <i class="fas fa-exclamation-circle text-warning"></i> Pendente
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-active">
                        <tr>
                            <td><strong>Totais:</strong></td>
                            <td>
                                <strong>R$ {{ number_format($profitBySession->sum('valor_total'), 2, ',', '.') }}</strong>
                            </td>
                            <td>
                                <strong>R$ {{ number_format($profitBySession->sum('valor_pago'), 2, ',', '.') }}</strong>
                            </td>
                            <td>
                                <strong>
                                    R$ {{ number_format($profitBySession->sum('valor_total') - $profitBySession->sum('valor_pago'), 2, ',', '.') }}
                                </strong>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <!-- Gráfico de Status -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <h6 class="text-muted">Status das Sessões</h6>
                    @php
                        $totalSessions = $profitBySession->count();
                        $paidSessions = $profitBySession->filter(fn($s) => ($s->valor_total - $s->valor_pago) <= 0)->count();
                        $pendingSessions = $totalSessions - $paidSessions;
                        $paidPercentage = $totalSessions > 0 ? ($paidSessions / $totalSessions) * 100 : 0;
                    @endphp
                    <div class="progress mb-2" style="height: 30px;">
                        <div class="progress-bar bg-success" style="width: {{ $paidPercentage }}%">
                            {{ $paidSessions }} Pagas ({{ number_format($paidPercentage, 1) }}%)
                        </div>
                        <div class="progress-bar bg-warning" style="width: {{ 100 - $paidPercentage }}%">
                            {{ $pendingSessions }} Pendentes ({{ number_format(100 - $paidPercentage, 1) }}%)
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">Situação Financeira</h6>
                    @php
                        $totalValue = $profitBySession->sum('valor_total');
                        $paidValue = $profitBySession->sum('valor_pago');
                        $paidValuePercentage = $totalValue > 0 ? ($paidValue / $totalValue) * 100 : 0;
                    @endphp
                    <div class="progress" style="height: 30px;">
                        <div class="progress-bar bg-success" style="width: {{ $paidValuePercentage }}%">
                            {{ number_format($paidValuePercentage, 1) }}% Recebido
                        </div>
                    </div>
                    <small class="text-muted">
                        R$ {{ number_format($paidValue, 2, ',', '.') }} de R$ {{ number_format($totalValue, 2, ',', '.') }}
                    </small>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Insights -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-lightbulb"></i> Destaques Positivos</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    @if($profitBySession->isNotEmpty())
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            {{ $paidSessions }} sessões completamente pagas
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            R$ {{ number_format($paidValue, 2, ',', '.') }} já recebido
                        </li>
                        <li>
                            <i class="fas fa-check text-success"></i>
                            Taxa de recebimento: {{ number_format($paidValuePercentage, 1) }}%
                        </li>
                    @else
                        <li class="text-muted">Nenhuma sessão com valores para análise</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Pontos de Atenção</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    @if($profitBySession->isNotEmpty())
                        @if($pendingSessions > 0)
                            <li class="mb-2">
                                <i class="fas fa-exclamation text-warning"></i>
                                {{ $pendingSessions }} sessões com saldo pendente
                            </li>
                        @endif
                        @php
                            $pendingValue = $totalValue - $paidValue;
                        @endphp
                        @if($pendingValue > 0)
                            <li class="mb-2">
                                <i class="fas fa-exclamation text-warning"></i>
                                R$ {{ number_format($pendingValue, 2, ',', '.') }} a receber
                            </li>
                        @endif
                        @if($pendingSessions == 0 && $pendingValue == 0)
                            <li class="text-success">
                                <i class="fas fa-check-circle"></i>
                                Sem pendências no momento!
                            </li>
                        @endif
                    @else
                        <li class="text-muted">Sem dados para análise</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    .btn, nav, footer {
        display: none !important;
    }
}
</style>
@endpush