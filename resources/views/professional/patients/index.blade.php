@extends('layouts.professional.app')

@section('title', 'Pacientes - Profissional')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2 class="mb-0"><i class="fas fa-users"></i> Meus Pacientes</h2>
    </div>
    <div class="col-auto">
        <a href="{{ route('professional.patients.create') }}" class="btn btn-sm btn-gradient">
            <i class="fas fa-user-plus"></i> Novo Paciente
        </a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card">
    <div class="table-responsive">
        @if ($patients->count() > 0)
            <table class="table table-hover table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Paciente</th>
                        <th class="d-none d-sm-table-cell">Contato</th>
                        <th class="d-none d-md-table-cell">CPF</th>
                        <th class="d-none d-lg-table-cell">Endereço</th>
                        <th class="text-end">Sessões</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($patients as $patient)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $patient->nome }}</div>
                                @if(!empty($patient->email))
                                    <small class="text-muted d-block">{{ $patient->email }}</small>
                                @endif
                                <!-- Mobile: Contato info inline -->
                                <div class="d-sm-none">
                                    <small class="text-muted d-block">{{ $patient->telefone ?? '—' }}</small>
                                    @if(!empty($patient->cpf))
                                        <small class="text-muted d-block">{{ $patient->cpf }}</small>
                                    @endif
                                </div>
                            </td>
                            <td class="d-none d-sm-table-cell">
                                <small class="text-muted">{{ $patient->telefone ?? '—' }}</small>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <small class="text-muted">{{ $patient->cpf ?? '—' }}</small>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                @php
                                    $primary = $patient->addresses->firstWhere('principal', true) ?? $patient->addresses->first();
                                @endphp

                                @if($primary)
                                    <small>
                                        {{ $primary->logradouro ?? $primary->rua }}, {{ $primary->numero }}<br>
                                        {{ $primary->bairro }} - {{ $primary->cidade }}
                                    </small>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <span class="badge bg-secondary">{{ $patient->therapySessions->count() ?? 0 }}</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm flex-wrap" role="group">
                                    <a href="{{ route('professional.patients.show', $patient) }}" 
                                       class="btn btn-outline-primary" 
                                       title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('professional.patients.edit', $patient) }}" 
                                       class="btn btn-outline-secondary d-none d-sm-inline-block" 
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="p-5 text-center">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">Nenhum paciente encontrado.</p>
            </div>
        @endif
    </div>
</div>

@if ($patients->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $patients->links('pagination::bootstrap-5') }}
    </div>
@endif
@endsection

@push('styles')
<style>
    .table-hover tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.04);
    }
    .badge {
        font-size: 0.8rem;
        padding: 0.35rem 0.5rem;
    }
</style>
@endpush
