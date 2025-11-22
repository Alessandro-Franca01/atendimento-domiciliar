@extends('layouts.app')

@section('title', 'Paciente: ' . $patient->nome)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        <i class="fas fa-user"></i> {{ $patient->nome }}
    </h1>
    <div>
        <a href="{{ route('patients.edit', $patient) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Editar
        </a>
        <a href="{{ route('patients.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<div class="row">
    <!-- Informações do Paciente -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Dados Pessoais</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Nome:</strong>
                    <p class="mb-0">{{ $patient->nome }}</p>
                </div>
                <div class="mb-3">
                    <strong>Telefone:</strong>
                    <p class="mb-0">{{ $patient->telefone }}</p>
                </div>
                <div class="mb-3">
                    <strong>CPF:</strong>
                    <p class="mb-0">{{ $patient->cpf }}</p>
                </div>
                @if(!empty($patient->email))
                    <div class="mb-3">
                        <strong>E-mail:</strong>
                        <p class="mb-0">{{ $patient->email }}</p>
                    </div>
                @endif
                @if(!empty($patient->numero_whatsapp))
                    <div class="mb-3">
                        <strong>WhatsApp:</strong>
                        <p class="mb-0">{{ $patient->numero_whatsapp }}</p>
                    </div>
                @endif
                <div class="mb-3">
                    <strong>Status:</strong>
                    <p class="mb-0">
                        @if($patient->status == 'ativo')
                            <span class="badge bg-success">Ativo</span>
                        @else
                            <span class="badge bg-secondary">Inativo</span>
                        @endif
                    </p>
                </div>
                @if($patient->observacoes)
                    <div>
                        <strong>Observações:</strong>
                        <p class="mb-0">{{ $patient->observacoes }}</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Estatísticas -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Estatísticas</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span>Total de Sessões:</span>
                    <strong>{{ $patient->therapySessions->count() }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Sessões Ativas:</span>
                    <strong>{{ $patient->therapySessions->where('status', 'ativo')->count() }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Agendamentos:</span>
                    <strong>{{ $patient->appointments->count() }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Atendimentos:</span>
                    <strong>{{ $patient->attendances->count() }}</strong>
                </div>
            </div>
        </div>
        
        <!-- Ações Rápidas -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt"></i> Ações Rápidas</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('therapy-sessions.create', ['patient_id' => $patient->id]) }}" 
                   class="btn btn-primary w-100 mb-2">
                    <i class="fas fa-plus"></i> Nova Sessão
                </a>
                <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" 
                   class="btn btn-success w-100 mb-2">
                    <i class="fas fa-calendar-plus"></i> Novo Agendamento
                </a>
                <a href="{{ route('attendances.create', ['patient_id' => $patient->id]) }}" 
                   class="btn btn-info w-100">
                    <i class="fas fa-file-medical"></i> Novo Atendimento
                </a>
            </div>
        </div>
    </div>
    
    <!-- Conteúdo Principal -->
    <div class="col-md-8">
        <!-- Endereços -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Endereços</h5>
                <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                    <i class="fas fa-plus"></i> Adicionar
                </button>
            </div>
            <div class="card-body">
                @if($patient->addresses->isEmpty())
                    <p class="text-muted mb-0">Nenhum endereço cadastrado</p>
                @else
                    <div class="row">
                        @foreach($patient->addresses as $address)
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <span class="badge bg-primary">{{ ucfirst($address->tipo) }}</span>
                                        </h6>
                                        <p class="mb-1">{{ $address->logradouro }}, {{ $address->numero }}</p>
                                        @if($address->complemento)
                                            <p class="mb-1">{{ $address->complemento }}</p>
                                        @endif
                                        <p class="mb-1">{{ $address->bairro }}</p>
                                        <p class="mb-1">{{ $address->cidade }}</p>
                                        <p class="mb-0"><small>CEP: {{ $address->cep }}</small></p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Sessões -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> Sessões de Terapia</h5>
            </div>
            <div class="card-body">
                @if($patient->therapySessions->isEmpty())
                    <p class="text-muted mb-0">Nenhuma sessão cadastrada</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Descrição</th>
                                    <th>Profissional</th>
                                    <th>Progresso</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($patient->therapySessions->sortByDesc('created_at') as $session)
                                    <tr>
                                        <td>{{ $session->id }}</td>
                                        <td>{{ $session->descricao }}</td>
                                        <td>{{ $session->professional->nome }}</td>
                                        <td>
                                            <small>{{ $session->sessoes_realizadas }}/{{ $session->total_sessoes }}</small>
                                        </td>
                                        <td>
                                            @if($session->status == 'ativo')
                                                <span class="badge bg-success">Ativo</span>
                                            @elseif($session->status == 'concluido')
                                                <span class="badge bg-secondary">Concluído</span>
                                            @else
                                                <span class="badge bg-warning">Suspenso</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('therapy-sessions.show', $session) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Agendamentos Recentes -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Próximos Agendamentos</h5>
            </div>
            <div class="card-body">
                @php
                    $upcomingAppointments = $patient->appointments()
                        ->where('data_hora_inicio', '>=', now())
                        ->orderBy('data_hora_inicio')
                        ->limit(5)
                        ->get();
                @endphp
                
                @if($upcomingAppointments->isEmpty())
                    <p class="text-muted mb-0">Nenhum agendamento próximo</p>
                @else
                    <div class="list-group">
                        @foreach($upcomingAppointments as $appointment)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $appointment->data_hora_inicio->format('d/m/Y H:i') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $appointment->professional->nome }}</small>
                                    </div>
                                    <div>
                                        @if($appointment->status == 'confirmado')
                                            <span class="badge bg-success">Confirmado</span>
                                        @else
                                            <span class="badge bg-primary">Agendado</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Adicionar Endereço -->
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar Endereço</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('patients.addresses.store', $patient) }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Logradouro *</label>
                            <input type="text" name="logradouro" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Número *</label>
                            <input type="text" name="numero" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Complemento</label>
                            <input type="text" name="complemento" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bairro *</label>
                            <input type="text" name="bairro" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cidade *</label>
                            <input type="text" name="cidade" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">CEP *</label>
                            <input type="text" name="cep" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipo *</label>
                            <select name="tipo" class="form-select" required>
                                <option value="casa">Casa</option>
                                <option value="trabalho">Trabalho</option>
                                <option value="familiar">Familiar</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Endereço</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection