@extends('layouts.app')

@section('title', 'Registrar Pagamento')

@section('content')
<h1 class="h4 mb-3">Registrar Pagamento</h1>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('pagamentos.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Paciente</label>
                        <select name="patient_id" class="form-select" required>
                        <option value="">Selecione...</option>
                            @foreach($patients as $p)
                            <option value="{{ $p->id }}">{{ $p->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Profissional</label>
                        <select name="professional_id" class="form-select" required>
                        <option value="">Selecione...</option>
                            @foreach($professionals as $pr)
                            <option value="{{ $pr->id }}">{{ $pr->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Método de Pagamento</label>
                    <select name="metodo_pagamento" class="form-select" required>
                        <option value="pix">PIX</option>
                        <option value="dinheiro">Dinheiro</option>
                        <option value="cartao">Cartão</option>
                        <option value="transferencia">Transferência</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sessão (Pacote)</label>
                    <select name="sessao_id" class="form-select">
                        <option value="">—</option>
                        @foreach($sessoes as $s)
                            <option value="{{ $s->id }}">#{{ $s->id }} - {{ $s->descricao }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">Se preencher, o pagamento será vinculado ao pacote.</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Atendimento Avulso</label>
                    <select name="atendimento_id" class="form-select">
                        <option value="">—</option>
                        @foreach($atendimentos as $a)
                            <option value="{{ $a->id }}">#{{ $a->id }} - {{ \Carbon\Carbon::parse($a->data_realizacao)->format('d/m/Y H:i') }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">Preencha apenas se não for pagamento por sessão.</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Valor (R$)</label>
                    <input type="number" step="0.01" min="0" name="valor" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Data do Pagamento</label>
                    <input type="date" name="data_pagamento" class="form-control">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Observações</label>
                    <textarea name="observacoes" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                <a href="{{ route('pagamentos.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
 </div>
@endsection