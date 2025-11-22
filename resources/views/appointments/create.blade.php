@extends('layouts.app')

@section('title', 'Novo Atendimento')

@section('content')
<h1 class="h4 mb-3">Novo Atendimento</h1>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('atendimentos.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Paciente</label>
                    <select name="patient_id" class="form-select" required>
                        <option value="">Selecione...</option>
                        @foreach($pacientes as $p)
                            <option value="{{ $p->id }}">{{ $p->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Profissional</label>
                    <select name="professional_id" class="form-select" required>
                        <option value="">Selecione...</option>
                        @foreach($profissionals as $pr)
                            <option value="{{ $pr->id }}">{{ $pr->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Agendamento (opcional)</label>
                    <select name="agendamento_id" class="form-select">
                        <option value="">—</option>
                        @foreach($agendamentos as $a)
                            <option value="{{ $a->id }}">#{{ $a->id }} - {{ $a->data_hora_inicio?->format('d/m/Y H:i') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Data de Realização</label>
                    <input type="datetime-local" name="data_realizacao" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Valor (R$)</label>
                    <input type="number" step="0.01" min="0" name="valor" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="concluido">Concluído</option>
                        <option value="interrompido">Interrompido</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Procedimento Realizado</label>
                    <textarea name="procedimento_realizado" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Evolução</label>
                    <textarea name="evolucao" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                <a href="{{ route('atendimentos.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
 </div>
@endsection