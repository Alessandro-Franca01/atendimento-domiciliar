@extends('layouts.app')

@section('title', 'Gerar Fatura Mensal')

@section('content')
<h1 class="h4 mb-3">Gerar Fatura Mensal</h1>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('faturas.mensal.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Paciente</label>
                    <select name="paciente_id" class="form-select" required>
                        <option value="">Selecione...</option>
                        @foreach($pacientes as $p)
                            <option value="{{ $p->id }}">{{ $p->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Mês de Referência</label>
                    <input type="month" name="mes" class="form-control" required>
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary"><i class="fas fa-file-invoice-dollar"></i> Gerar</button>
                <a href="{{ route('faturas.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
 </div>
@endsection