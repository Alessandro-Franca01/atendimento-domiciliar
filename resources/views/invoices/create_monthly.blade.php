@extends('layouts.app')

@section('title', 'Gerar Fatura Mensal')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        <i class="fas fa-file-invoice-dollar"></i> Gerar Fatura Mensal
    </h1>
    <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>Como funciona:</strong> A fatura mensal agrupa todos os atendimentos realizados para um paciente específico em um determinado mês.
        </div>
        
        <form method="POST" action="{{ route('invoices.monthly.store') }}">
            @csrf
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Paciente *</label>
                    <select name="patient_id" 
                            id="patient_id"
                            class="form-select @error('patient_id') is-invalid @enderror" 
                            required>
                        <option value="">Selecione um paciente...</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                {{ $patient->nome }} - {{ $patient->cpf }}
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Mês de Referência *</label>
                    <input type="month" 
                           name="mes" 
                           class="form-control @error('mes') is-invalid @enderror" 
                           value="{{ old('mes', now()->format('Y-m')) }}"
                           required>
                    @error('mes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        Selecione o mês para o qual deseja gerar a fatura
                    </small>
                </div>
            </div>
            
            <!-- Preview dos Atendimentos -->
            <div id="preview" class="mt-4" style="display: none;">
                <hr>
                <h5 class="text-primary">
                    <i class="fas fa-eye"></i> Pré-visualização
                </h5>
                <div id="preview-content">
                    <div class="text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-success" id="submitBtn">
                    <i class="fas fa-file-invoice"></i> Gerar Fatura
                </button>
                <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const patientSelect = document.getElementById('patient_id');
    const monthInput = document.querySelector('[name="mes"]');
    const preview = document.getElementById('preview');
    const previewContent = document.getElementById('preview-content');
    const submitBtn = document.getElementById('submitBtn');
    
    function loadPreview() {
        const patientId = patientSelect.value;
        const month = monthInput.value;
        
        if (!patientId || !month) {
            preview.style.display = 'none';
            return;
        }
        
        preview.style.display = 'block';
        previewContent.innerHTML = `
            <div class="text-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
            </div>
        `;
        
        // Simular busca de atendimentos (você pode implementar uma rota API real)
        setTimeout(() => {
            previewContent.innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    Pronto para gerar a fatura para o mês selecionado.
                </div>
                <p class="text-muted mb-0">
                    <i class="fas fa-info-circle"></i>
                    A fatura será gerada com todos os atendimentos realizados no período.
                </p>
            `;
        }, 500);
    }
    
    patientSelect.addEventListener('change', loadPreview);
    monthInput.addEventListener('change', loadPreview);
});
</script>
@endpush