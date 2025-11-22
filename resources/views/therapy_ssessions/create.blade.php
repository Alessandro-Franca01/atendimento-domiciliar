@extends('layouts.app')

@section('title', 'Nova Sessão')

@section('content')
<h1 class="h4 mb-3">Nova Sessão de Terapia</h1>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('therapy-sessions.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Paciente *</label>
                    <select name="patient_id" class="form-select @error('patient_id') is-invalid @enderror" required>
                        <option value="">Selecione...</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
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
                    <select name="professional_id" class="form-select @error('professional_id') is-invalid @enderror" required>
                        <option value="">Selecione...</option>
                        @foreach($professionals as $professional)
                            <option value="{{ $professional->id }}" {{ old('professional_id') == $professional->id ? 'selected' : '' }}>
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
                           value="{{ old('descricao') }}"
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
                           value="{{ old('total_sessoes') }}"
                           required>
                    @error('total_sessoes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Data de Início *</label>
                    <input type="date" 
                           name="data_inicio" 
                           class="form-control @error('data_inicio') is-invalid @enderror" 
                           value="{{ old('data_inicio', now()->format('Y-m-d')) }}"
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
                           value="{{ old('data_fim_prevista') }}">
                    @error('data_fim_prevista')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Valor por Sessão (R$)</label>
                    <input type="number" 
                           step="0.01" 
                           min="0" 
                           name="valor_por_sessao" 
                           class="form-control @error('valor_por_sessao') is-invalid @enderror"
                           value="{{ old('valor_por_sessao') }}">
                    <div class="form-text">Opcional. Calcula automaticamente o valor do pacote.</div>
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
                           value="{{ old('desconto_percentual', 0) }}">
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
                           value="{{ old('desconto_valor') }}">
                    <div class="form-text">Tem prioridade sobre o desconto percentual.</div>
                    @error('desconto_valor')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar Sessão
                </button>
                <a href="{{ route('therapy-sessions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection