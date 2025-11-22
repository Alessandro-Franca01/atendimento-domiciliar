@extends('layouts.app')

@section('title', 'Novo Atendimento')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        <i class="fas fa-file-medical"></i> Registrar Atendimento
    </h1>
    <a href="{{ route('attendances.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('attendances.store') }}">
            @csrf
            
            <div class="row g-3">
                <!-- Agendamento -->
                @if(request('appointment_id'))
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Atendimento vinculado ao agendamento #{{ request('appointment_id') }}
                        </div>
                        <input type="hidden" name="appointment_id" value="{{ request('appointment_id') }}">
                    </div>
                @else
                    <div class="col-md-12">
                        <label class="form-label">Agendamento (opcional)</label>
                        <select name="appointment_id" 
                                id="appointment_id"
                                class="form-select @error('appointment_id') is-invalid @enderror">
                            <option value="">Atendimento avulso</option>
                            @foreach($appointments as $appt)
                                <option value="{{ $appt->id }}"
                                        data-patient="{{ $appt->patient_id }}"
                                        data-professional="{{ $appt->professional_id }}"
                                        {{ old('appointment_id') == $appt->id ? 'selected' : '' }}>
                                    #{{ $appt->id }} - {{ $appt->patient->nome }} - 
                                    {{ $appt->data_hora_inicio->format('d/m/Y H:i') }}
                                </option>
                            @endforeach
                        </select>
                        @error('appointment_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif
                
                <!-- Paciente e Profissional -->
                <div class="col-md-6">
                    <label class="form-label">Paciente *</label>
                    <select name="patient_id" 
                            id="patient_id"
                            class="form-select @error('patient_id') is-invalid @enderror" 
                            required>
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
                    <select name="professional_id" 
                            id="professional_id"
                            class="form-select @error('professional_id') is-invalid @enderror" 
                            required>
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
                
                <!-- Data e Hora -->
                <div class="col-md-6">
                    <label class="form-label">Data de Realização *</label>
                    <input type="date" 
                           name="date" 
                           id="date"
                           class="form-control @error('data_realizacao') is-invalid @enderror" 
                           value="{{ old('date', now()->format('Y-m-d')) }}"
                           required>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Hora *</label>
                    <input type="time" 
                           name="time" 
                           id="time"
                           class="form-control @error('data_realizacao') is-invalid @enderror" 
                           value="{{ old('time', now()->format('H:i')) }}"
                           required>
                    @error('data_realizacao')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <input type="hidden" name="data_realizacao" id="data_realizacao">
                
                <!-- Valor -->
                <div class="col-md-6">
                    <label class="form-label">Valor (R$)</label>
                    <input type="number" 
                           step="0.01" 
                           min="0" 
                           name="valor" 
                           class="form-control @error('valor') is-invalid @enderror"
                           value="{{ old('valor') }}">
                    @error('valor')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Deixe em branco se já estiver incluído no valor da sessão</small>
                </div>
                
                <!-- Status -->
                <div class="col-md-6">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="concluido" {{ old('status', 'concluido') == 'concluido' ? 'selected' : '' }}>
                            Concluído
                        </option>
                        <option value="interrompido" {{ old('status') == 'interrompido' ? 'selected' : '' }}>
                            Interrompido
                        </option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Procedimento Realizado -->
                <div class="col-md-12">
                    <label class="form-label">Procedimento Realizado *</label>
                    <textarea name="procedimento_realizado" 
                              class="form-control @error('procedimento_realizado') is-invalid @enderror" 
                              rows="4"
                              required>{{ old('procedimento_realizado') }}</textarea>
                    @error('procedimento_realizado')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        Descreva os procedimentos realizados durante o atendimento
                    </small>
                </div>
                
                <!-- Evolução -->
                <div class="col-md-12">
                    <label class="form-label">Evolução do Paciente *</label>
                    <textarea name="evolucao" 
                              class="form-control @error('evolucao') is-invalid @enderror" 
                              rows="4"
                              required>{{ old('evolucao') }}</textarea>
                    @error('evolucao')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        Registre a evolução e observações sobre o quadro clínico do paciente
                    </small>
                </div>
                
                <!-- Assinatura do Paciente -->
                <div class="col-md-12">
                    <label class="form-label">Assinatura do Paciente</label>
                    <input type="text" 
                           name="assinatura_paciente" 
                           class="form-control @error('assinatura_paciente') is-invalid @enderror"
                           value="{{ old('assinatura_paciente') }}"
                           placeholder="Nome completo do paciente confirmando o atendimento">
                    @error('assinatura_paciente')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Registrar Atendimento
                </button>
                <a href="{{ route('attendances.index') }}" class="btn btn-secondary">
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
    const appointmentSelect = document.getElementById('appointment_id');
    const patientSelect = document.getElementById('patient_id');
    const professionalSelect = document.getElementById('professional_id');
    const form = document.querySelector('form');
    
    // Preencher dados quando selecionar agendamento
    if (appointmentSelect) {
        appointmentSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const patientId = selectedOption.dataset.patient;
            const professionalId = selectedOption.dataset.professional;
            
            if (patientId) {
                patientSelect.value = patientId;
            }
            
            if (professionalId) {
                professionalSelect.value = professionalId;
            }
        });
    }
    
    // Combinar data e hora antes de enviar
    form.addEventListener('submit', function(e) {
        const date = document.getElementById('date').value;
        const time = document.getElementById('time').value;
        
        document.getElementById('data_realizacao').value = `${date} ${time}:00`;
    });
});
</script>
@endpush