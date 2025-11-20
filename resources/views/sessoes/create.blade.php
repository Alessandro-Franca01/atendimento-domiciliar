@extends('layouts.app')

@section('title', 'Nova Sessão')

@section('content')
<h1 class="h4 mb-3">Nova Sessão (Pacote)</h1>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('sessoes.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Paciente</label>
                        <select name="patient_id" class="form-select" required>
                            <option value="">Selecione...</option>
                            @foreach($pacientes as $p)
                                <option value="{{ $p->id }}">{{ $p->nome }}</option>
                            @endforeach
                        </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Profissional</label>
                        <select name="professional_id" class="form-select" required>
                            <option value="">Selecione...</option>
                            @foreach($profissionals as $pr)
                                <option value="{{ $pr->id }}">{{ $pr->nome }}</option>
                            @endforeach
                        </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Descrição</label>
                    <input type="text" name="descricao" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Total de Sessões</label>
                    <input type="number" name="total_sessoes" class="form-control" min="1" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Data de Início</label>
                    <input type="date" name="data_inicio" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Previsão de Término</label>
                    <input type="date" name="data_fim_prevista" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Valor por Sessão (R$)</label>
                    <input type="number" step="0.01" min="0" name="valor_por_sessao" class="form-control">
                    <div class="form-text">Opcional. Calcula automaticamente o valor do pacote.</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Desconto (%)</label>
                    <input type="number" step="0.01" min="0" max="100" name="desconto_percentual" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Desconto Fixo (R$)</label>
                    <input type="number" step="0.01" min="0" name="desconto_valor" class="form-control">
                    <div class="form-text">Tem prioridade sobre o desconto percentual.</div>
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                <a href="{{ route('sessoes.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
 </div>
@endsection