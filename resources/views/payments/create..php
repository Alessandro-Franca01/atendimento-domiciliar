@extends('layouts.app')

@section('title', 'Novo Pagamento')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        <i class="fas fa-dollar-sign"></i> Registrar Pagamento
    </h1>
    <a href="{{ route('payments.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('payments.store') }}">
            @csrf
            
            <div class="row g-3">
                <!-- Seleção do Tipo -->
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Dica:</strong> Selecione o paciente e profissional primeiro, depois escolha se o pagamento é para uma sessão ou atendimento específico.
                    </div>
                </div>
                
                <!-- Paciente e Profissional -->
                <div class="col-md-6">
                    <label class="form-label">Paciente *</label>
                    <select name="patient_id" 
                            class="form-select @error('patient_id') is-invalid @enderror" 
                            required>
                        <option value="">Selecione...</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ old('patient_id', request('patient_id')) == $patient->id ? 'selected' : '' }}>
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
                            required>
                        <option value="">Selecione...</option>
                        @foreach($professionals as $professional)
                            <option value="{{ $professional->id }}" {{ old('professional_id', request('professional_id')) == $professional->id ? 'selected' : '' }}>
                                {{ $professional->nome }}
                            </option>
                        @endforeach
                    </select>
                    @error('professional_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Sessão ou Atendimento -->
                <div class="col-md-6">
                    <label class="form-label">Sessão (opcional)</label>
                    <select name="therapy_session_id" 
                            class="form-select @error('therapy_session_id') is-invalid @enderror">
                        <option value="">Nenhuma</option>
                        @foreach($sessions as $session)
                            <option value="{{ $session->id }}" 
                                    {{ old('therapy_session_id', request('therapy_session_id')) == $session->id ? 'selected' : '' }}>
                                #{{ $session->id }} - {{ $session->patient->nome }} - {{ $session->descricao }}
                                (Saldo: R$ {{ number_format($session->saldo_pagamento, 2, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    @error('therapy_session_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Pagamento para o pacote de sessões</small>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Atendimento (opcional)</label>
                    <select name="attendance_id" 
                            class="form-select @error('attendance_id') is-invalid @enderror">
                        <option value="">Nenhum</option>
                        @foreach($attendances as $attendance)
                            <option value="{{ $attendance->id }}" 
                                    {{ old('attendance_id', request('attendance_id')) == $attendance->id ? 'selected' : '' }}>
                                #{{ $attendance->id }} - {{ $attendance->patient->nome }} - 
                                {{ $attendance->data_realizacao->format('d/m/Y') }}
                            </option>
                        @endforeach
                    </select>
                    @error('attendance_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Pagamento para atendimento avulso</small>
                </div>
                
                <!-- Dados do Pagamento -->
                <div class="col-md-4">
                    <label class="form-label">Valor (R$) *</label>
                    <input type="number" 
                           step="0.01" 
                           min="0.01" 
                           name="valor" 
                           class="form-control @error('valor') is-invalid @enderror" 
                           value="{{ old('valor', request('valor')) }}"
                           required>
                    @error('valor')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Método de Pagamento *</label>
                    <select name="metodo_pagamento" 
                            class="form-select @error('metodo_pagamento') is-invalid @enderror" 
                            required>
                        <option value="">Selecione...</option>
                        <option value="pix" {{ old('metodo_pagamento') == 'pix' ? 'selected' : '' }}>PIX</option>
                        <option value="dinheiro" {{ old('metodo_pagamento') == 'dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                        <option value="cartao" {{ old('metodo_pagamento') == 'cartao' ? 'selected' : '' }}>Cartão</option>
                        <option value="transferencia" {{ old('metodo_pagamento') == 'transferencia' ? 'selected' : '' }}>Transferência</option>
                    </select>
                    @error('metodo_pagamento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Data do Pagamento *</label>
                    <input type="date" 
                           name="data_pagamento" 
                           class="form-control @error('data_pagamento') is-invalid @enderror" 
                           value="{{ old('data_pagamento', now()->format('Y-m-d')) }}"
                           required>
                    @error('data_pagamento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Fatura (opcional) -->
                <div class="col-md-12">
                    <label class="form-label">Fatura (opcional)</label>
                    <select name="invoice_id" 
                            class="form-select @error('invoice_id') is-invalid @enderror">
                        <option value="">Nenhuma</option>
                        @if(isset($invoices))
                            @foreach($invoices as $invoice)
                                <option value="{{ $invoice->id }}" {{ old('invoice_id') == $invoice->id ? 'selected' : '' }}>
                                    #{{ $invoice->id }} - {{ $invoice->patient->nome }} - 
                                    R$ {{ number_format($invoice->valor_total, 2, ',', '.') }} - 
                                    {{ $invoice->status }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('invoice_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Observações -->
                <div class="col-md-12">
                    <label class="form-label">Observações</label>
                    <textarea name="observacoes" 
                              class="form-control @error('observacoes') is-invalid @enderror" 
                              rows="3">{{ old('observacoes') }}</textarea>
                    @error('observacoes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check"></i> Registrar Pagamento
                </button>
                <a href="{{ route('payments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Limpar sessão quando selecionar atendimento e vice-versa
    document.querySelector('[name="therapy_session_id"]').addEventListener('change', function() {
        if(this.value) {
            document.querySelector('[name="attendance_id"]').value = '';
        }
    });
    
    document.querySelector('[name="attendance_id"]').addEventListener('change', function() {
        if(this.value) {
            document.querySelector('[name="therapy_session_id"]').value = '';
        }
    });
</script>
@endpush