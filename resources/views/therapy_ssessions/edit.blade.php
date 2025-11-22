@extends('layouts.app')

@section('title', 'Editar Sessão #' . $therapySession->id)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        <i class="fas fa-edit"></i> Editar Sessão #{{ $therapySession->id }}
    </h1>
    <a href="{{ route('therapy-sessions.show', $therapySession) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
</div>

@if($therapySession->status === 'concluido')
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Atenção:</strong> Esta sessão está concluída. Algumas informações não podem ser alteradas.
    </div>
@endif

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('therapy-sessions.update', $therapySession) }}">
            @csrf
            @method('PUT')
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Paciente *</label>
                    <select name="patient_id" 
                            class="form-select @error('patient_id') is-invalid @enderror" 
                            required
                            {{ $therapySession->status === 'concluido' ? 'disabled' : '' }}>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ $therapySession->patient_id == $patient->id ? 'selected' : '' }}>
                                {{ $patient->nome }}
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Profissional *</label>
                    <select name="professional_id" 
                            class="form-select @error('professional_id') is-invalid @enderror" 
                            required
                            {{ $therapySession->status === 'concluido' ? 'disabled' : '' }}>
                        @foreach($professionals as $professional)
                            <option value="{{ $professional->id }}" {{ $therapySession->professional_id == $professional->id ? 'selected' : '' }}>
                                {{ $professional->nome }}
                            </option>
                        @endforeach
                    </select>
                    @error('professional_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-12">
                    <label class="form-label">Descrição *</label>
                    <input type="text" 
                           name="descricao" 
                           class="form-control @error('descricao') is-invalid @enderror" 
                           value="{{ old('descricao', $therapySession->descricao) }}"
                           required>
                    @error('descricao')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Total de Sessões *</label>
                    <input type="number" 
                           name="total_sessoes" 
                           class="form-control @error('total_sessoes') is-invalid @enderror" 
                           min="1" 
                           value="{{ old('total_sessoes', $therapySession->total_sessoes) }}"
                           required
                           {{ $therapySession->status === 'concluido' ? 'readonly' : '' }}>
                    @error('total_sessoes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        Realizadas: {{ $therapySession->sessoes_realizadas }}
                    </small>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Data de Início *</label>
                    <input type="date" 
                           name="data_inicio" 
                           class="form-control @error('data_inicio') is-invalid @enderror" 
                           value="{{ old('data_inicio', $therapySession->data_inicio->format('Y-m-d')) }}"
                           required>
                    @error('data_inicio')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Previsão de Término</label>
                    <input type="date" 
                           name="data_fim_prevista" 
                           class="form-control @error('data_fim_prevista') is-invalid @enderror" 
                           value="{{ old('data_fim_prevista', $therapySession->data_fim_prevista?->format('Y-m-d')) }}">
                    @error('data_fim_prevista')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-12">
                    <hr>
                    <h5 class="text-primary">Valores</h5>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Valor por Sessão (R$)</label>
                    <input type="number" 
                           step="0.01" 
                           min="0" 
                           name="valor_por_sessao" 
                           class="form-control @error('valor_por_sessao') is-invalid @enderror"
                           value="{{ old('valor_por_sessao', $therapySession->valor_por_sessao) }}">
                    @error('valor_por_sessao')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Desconto (%)</label>
                    <input type="number" 
                           step="0.01" 
                           min="0" 
                           max="100" 
                           name="desconto_percentual" 
                           class="form-control @error('desconto_percentual') is-invalid @enderror"
                           value="{{ old('desconto_percentual', $therapySession->desconto_percentual) }}">
                    @error('desconto_percentual')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Desconto Fixo (R$)</label>
                    <input type="number" 
                           step="0.01" 
                           min="0" 
                           name="desconto_valor" 
                           class="form-control @error('desconto_valor') is-invalid @enderror"
                           value="{{ old('desconto_valor', $therapySession->desconto_valor) }}">
                    <div class="form-text">Tem prioridade sobre o desconto percentual.</div>
                    @error('desconto_valor')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                @if($therapySession->valor_total)
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Valor Total Atual:</strong> R$ {{ number_format($therapySession->valor_total, 2, ',', '.') }} |
                            <strong>Pago:</strong> R$ {{ number_format($therapySession->valor_pago, 2, ',', '.') }} |
                            <strong>Saldo:</strong> R$ {{ number_format($therapySession->saldo_pagamento, 2, ',', '.') }}
                        </div>
                    </div>
                @endif
                
                <div class="col-md-12">
                    <hr>
                    <h5 class="text-primary">Status</h5>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="ativo" {{ $therapySession->status == 'ativo' ? 'selected' : '' }}>Ativo</option>
                        <option value="suspenso" {{ $therapySession->status == 'suspenso' ? 'selected' : '' }}>Suspenso</option>
                        <option value="concluido" {{ $therapySession->status == 'concluido' ? 'selected' : '' }}>Concluído</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar Alterações
                </button>
                <a href="{{ route('therapy-sessions.show', $therapySession) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection