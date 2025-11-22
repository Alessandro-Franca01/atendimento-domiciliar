@extends('layouts.app')

@section('title', 'Fluxo de Caixa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        <i class="fas fa-chart-line"></i> Fluxo de Caixa
    </h1>
    <button class="btn btn-secondary" onclick="window.print()">
        <i class="fas fa-print"></i> Imprimir
    </button>
</div>

<!-- Resumo do Mês -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body text-center">
                <h5>Total Recebido no Mês</h5>
                <h2>R$ {{ number_format($totalMonth, 2, ',', '.') }}</h2>
                <small>{{ now()->format('F Y') }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-warning">
            <div class="card-body text-center">
                <h5>Previsão de Recebíveis</h5>
                <h2>R$ {{ number_format($receivablesForecast, 2, ',', '.') }}</h2>
                <small>Faturas abertas do mês</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info">
            <div class="card-body text-center">
                <h5>Total Previsto</h5>
                <h2>R$ {{ number_format($totalMonth + $receivablesForecast, 2, ',', '.') }}</h2>
                <small>Recebido + A receber</small>
            </div>
        </div>
    </div>
</div>

<!-- Gráfico Diário -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-calendar-day"></i> Recebimentos Diários do Mês</h5>
    </div>
    <div class="card-body">
        @if($daily->isEmpty())
            <p class="text-muted mb-0">Nenhum recebimento registrado neste mês.</p>
        @else
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Dia da Semana</th>
                            <th class="text-end">Total Recebido</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($daily as $day)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($day->dia)->format('d/m/Y') }}</td>
                                <td>
                                    @php
                                        $dayOfWeek = \Carbon\Carbon::parse($day->dia)->dayOfWeek;
                                        $days = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
                                    @endphp
                                    {{ $days[$dayOfWeek] }}
                                </td>
                                <td class="text-end">
                                    <strong>R$ {{ number_format($day->total, 2, ',', '.') }}</strong>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-active">
                        <tr>
                            <td colspan="2" class="text-end"><strong>Total do Mês:</strong></td>
                            <td class="text-end">
                                <h5 class="mb-0">R$ {{ number_format($totalMonth, 2, ',', '.') }}</h5>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <!-- Gráfico de Barras Simples -->
            <div class="mt-4">
                <h6 class="text-muted">Visualização Gráfica</h6>
                @php
                    $maxValue = $daily->max('total');
                @endphp
                @foreach($daily as $day)
                    <div class="mb-2">
                        <div class="d-flex align-items-center">
                            <div style="width: 80px;">
                                <small>{{ \Carbon\Carbon::parse($day->dia)->format('d/m') }}</small>
                            </div>
                            <div class="flex-grow-1">
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-success" 
                                         role="progressbar" 
                                         style="width: {{ ($day->total / $maxValue) * 100 }}%"
                                         aria-valuenow="{{ $day->total }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="{{ $maxValue }}">
                                        R$ {{ number_format($day->total, 2, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Análise por Período -->
<div class="card">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Análise do Período</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary">Estatísticas</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-calendar-alt text-muted"></i>
                        <strong>Dias com recebimento:</strong> {{ $daily->count() }}
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-chart-line text-muted"></i>
                        <strong>Média diária:</strong> 
                        R$ {{ number_format($daily->count() > 0 ? $totalMonth / $daily->count() : 0, 2, ',', '.') }}
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-arrow-up text-success"></i>
                        <strong>Melhor dia:</strong> 
                        @if($daily->count() > 0)
                            R$ {{ number_format($daily->max('total'), 2, ',', '.') }}
                        @else
                            -
                        @endif
                    </li>
                    <li>
                        <i class="fas fa-arrow-down text-danger"></i>
                        <strong>Pior dia:</strong> 
                        @if($daily->count() > 0)
                            R$ {{ number_format($daily->min('total'), 2, ',', '.') }}
                        @else
                            -
                        @endif
                    </li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6 class="text-primary">Projeções</h6>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Com a média atual de R$ {{ number_format($daily->count() > 0 ? $totalMonth / $daily->count() : 0, 2, ',', '.') }} por dia,
                    a projeção para o final do mês é de aproximadamente 
                    <strong>R$ {{ number_format(($daily->count() > 0 ? $totalMonth / $daily->count() : 0) * now()->daysInMonth, 2, ',', '.') }}</strong>
                </div>
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