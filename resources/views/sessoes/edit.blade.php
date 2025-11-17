@extends('layouts.app')

@section('title', 'Editar Sessão')

@section('content')
<h1 class="h4 mb-3">Editar Sessão #{{ $sessao->id }}</h1>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('sessoes.update', $sessao) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Paciente</label>
                    <select name="paciente_id" class="form-select" required>
                        @foreach($pacientes as $p)
                            <option value="{{ $p->id }}" @selected($sessao->paciente_id === $p->id)>{{ $p->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Profissional</label>
                    <select name="profissional_id" class="form-select" required>
                        @foreach($profissionals as $pr)
                            <option value="{{ $pr->id }}" @selected($sessao->profissional_id === $pr->id)>{{ $pr->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Descrição</label>
                    <input type="text" name="descricao" value="{{ $sessao->descricao }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Total de Sessões</label>
                    <input type="number" name="total_sessoes" value="{{ $sessao->total_sessoes }}" class="form-control" min="1" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Data de Início</label>
                    <input type="date" name="data_inicio" value="{{ $sessao->data_inicio?->format('Y-m-d') }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Previsão de Término</label>
                    <input type="date" name="data_fim_prevista" value="{{ $sessao->data_fim_prevista?->format('Y-m-d') }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Valor por Sessão (R$)</label>
                    <input type="number" step="0.01" min="0" name="valor_por_sessao" value="{{ $sessao->valor_por_sessao }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Desconto (%)</label>
                    <input type="number" step="0.01" min="0" max="100" name="desconto_percentual" value="{{ $sessao->desconto_percentual }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Desconto Fixo (R$)</label>
                    <input type="number" step="0.01" min="0" name="desconto_valor" value="{{ $sessao->desconto_valor }}" class="form-control">
                    <div class="form-text">Tem prioridade sobre o desconto percentual.</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="ativo" @selected($sessao->status==='ativo')>Ativo</option>
                        <option value="suspenso" @selected($sessao->status==='suspenso')>Suspenso</option>
                        <option value="concluido" @selected($sessao->status==='concluido')>Concluído</option>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                <a href="{{ route('sessoes.show', $sessao) }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
 </div>
@endsection