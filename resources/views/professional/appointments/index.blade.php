@extends('layouts.professional.app')

@section('title', 'Agendamentos - Profissional')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2 class="mb-0">
            <i class="fas fa-calendar-alt"></i> Meus Agendamentos
        </h2>
    </div>
    <div class="col-auto">
        <a href="{{ route('professional.appointments.today') }}" class="btn btn-sm btn-info me-2">
            <i class="fas fa-calendar-day"></i> Hoje
        </a>
        <a href="{{ route('professional.appointments.week') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-calendar-week"></i> Esta Semana
        </a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card">
    <div class="table-responsive">
        @if ($appointments->count() > 0)
            <table class="table table-hover table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>
                            <i class="fas fa-user"></i> Paciente
                        </th>
                        <th class="d-none d-md-table-cell">
                            <i class="fas fa-calendar"></i> Data/Hora
                        </th>
                        <th class="d-none d-lg-table-cell">
                            <i class="fas fa-map-marker-alt"></i> Local
                        </th>
                        <th class="d-none d-lg-table-cell">
                            <i class="fas fa-heartbeat"></i> Sessão
                        </th>
                        <th>
                            <span class="badge bg-secondary">Status</span>
                        </th>
                        <th>
                            <i class="fas fa-cogs"></i> Ações
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($appointments as $appointment)
                        <tr>
                            <td>
                                <div>
                                    <p class="mb-0 fw-bold">{{ $appointment->patient->nome }}</p>
                                    <small class="text-muted">
                                        {{ $appointment->patient->cpf }}
                                    </small>
                                    <div class="d-md-none mt-2">
                                        <small class="d-block text-muted">
                                            <strong>{{ \Carbon\Carbon::parse($appointment->data_hora_inicio)->format('d/m/Y H:i') }}</strong>
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <div>
                                    <p class="mb-0">
                                        <strong>{{ \Carbon\Carbon::parse($appointment->data_hora_inicio)->format('d/m/Y') }}</strong>
                                    </p>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($appointment->data_hora_inicio)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($appointment->data_hora_fim)->format('H:i') }}
                                    </small>
                                </div>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                @if ($appointment->address)
                                    <small>
                                        {{ $appointment->address->rua }}, {{ $appointment->address->numero }}<br>
                                        {{ $appointment->address->bairro }} - {{ $appointment->address->cidade }}
                                    </small>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td class="d-none d-lg-table-cell">
                                @if ($appointment->therapySession)
                                    <small>{{ $appointment->therapySession->tipo_sessao }}</small>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusConfig = [
                                        'agendado' => ['badge' => 'bg-warning', 'icon' => 'fa-clock', 'label' => 'Agendado'],
                                        'confirmado' => ['badge' => 'bg-info', 'icon' => 'fa-check-square', 'label' => 'Confirmado'],
                                        'concluido' => ['badge' => 'bg-success', 'icon' => 'fa-check-circle', 'label' => 'Concluído'],
                                        'cancelado' => ['badge' => 'bg-danger', 'icon' => 'fa-times-circle', 'label' => 'Cancelado'],
                                        'faltou' => ['badge' => 'bg-secondary', 'icon' => 'fa-user-slash', 'label' => 'Faltou'],
                                    ];
                                    $status = $statusConfig[$appointment->status] ?? $statusConfig['agendado'];
                                @endphp
                                <span class="badge {{ $status['badge'] }}">
                                    <i class="fas {{ $status['icon'] }}"></i> {{ $status['label'] }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm flex-wrap" role="group">
                                    <a href="{{ route('professional.appointments.show', $appointment) }}" 
                                       class="btn btn-outline-primary" 
                                       title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('professional.appointments.edit', $appointment) }}" 
                                       class="btn btn-outline-secondary d-none d-sm-inline-block" 
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-danger dropdown-toggle" 
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @if ($appointment->status !== 'confirmado' && $appointment->status !== 'concluido' && $appointment->status !== 'cancelado')
                                                <li>
                                                    <a class="dropdown-item" 
                                                       href="{{ route('professional.appointments.confirm', $appointment) }}"
                                                       onclick="return confirm('Deseja confirmar este agendamento?')">
                                                        <i class="fas fa-check-circle text-success"></i> Confirmar
                                                    </a>
                                                </li>
                                            @endif
                                            @if ($appointment->status !== 'concluido' && $appointment->status !== 'cancelado')
                                                <li>
                                                    <a class="dropdown-item text-danger" 
                                                       href="{{ route('professional.appointments.cancel', $appointment) }}"
                                                       onclick="return confirm('Deseja cancelar este agendamento?')">
                                                        <i class="fas fa-times-circle"></i> Cancelar
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="p-5 text-center">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">Você não possui agendamentos no momento.</p>
            </div>
        @endif
    </div>
</div>

@if ($appointments->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $appointments->links('pagination::bootstrap-5') }}
    </div>
@endif
@endsection

@push('styles')
<style>
    .table-hover tbody tr {
        transition: background-color 0.2s ease;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.05);
    }

    .btn-group-sm .btn {
        padding: 0.35rem 0.5rem;
        font-size: 0.75rem;
    }

    .badge {
        font-size: 0.8rem;
        padding: 0.4rem 0.6rem;
    }
</style>
@endpush
