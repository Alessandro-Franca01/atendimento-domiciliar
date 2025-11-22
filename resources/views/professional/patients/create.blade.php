@extends('layouts.professional.app')

@section('title', 'Novo Paciente - Profissional')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2 class="mb-0"><i class="fas fa-user-plus"></i> Novo Paciente</h2>
    </div>
    <div class="col-auto">
        <a href="{{ route('professional.patients.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Existem erros no formulário.</strong>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card p-4">
    <form action="{{ route('professional.patients.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-12 col-md-6 mb-3">
                <label class="form-label">Nome <span class="text-danger">*</span></label>
                <input type="text" name="nome" value="{{ old('nome') }}" class="form-control @error('nome') is-invalid @enderror" required>
                @error('nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-6 mb-3">
                <label class="form-label">E-mail</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-6 col-md-4 mb-3">
                <label class="form-label">Telefone <span class="text-danger">*</span></label>
                <input type="text" name="telefone" value="{{ old('telefone') }}" class="form-control @error('telefone') is-invalid @enderror" required>
                @error('telefone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-6 col-md-4 mb-3">
                <label class="form-label">Data de Nascimento</label>
                <input type="date" name="data_nascimento" value="{{ old('data_nascimento') }}" class="form-control @error('data_nascimento') is-invalid @enderror">
                @error('data_nascimento') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-6 col-md-4 mb-3">
                <label class="form-label">CPF</label>
                <input type="text" name="cpf" value="{{ old('cpf') }}" class="form-control @error('cpf') is-invalid @enderror">
                @error('cpf') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-6 mb-3">
                <label class="form-label">Convênio</label>
                <input type="text" name="convenio" value="{{ old('convenio') }}" class="form-control @error('convenio') is-invalid @enderror">
                @error('convenio') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-6 mb-3">
                <label class="form-label">Número da Carteirinha</label>
                <input type="text" name="numero_carteirinha" value="{{ old('numero_carteirinha') }}" class="form-control @error('numero_carteirinha') is-invalid @enderror">
                @error('numero_carteirinha') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-6 mb-3">
                <label class="form-label">Número (WhatsApp)</label>
                <input type="text" name="numero_whatsapp" value="{{ old('numero_whatsapp') }}" class="form-control @error('numero_whatsapp') is-invalid @enderror">
                @error('numero_whatsapp') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 mb-3">
                <label class="form-label">Observações</label>
                <textarea name="observacoes" class="form-control @error('observacoes') is-invalid @enderror" rows="3">{{ old('observacoes') }}</textarea>
                @error('observacoes') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <hr>
        <h5>Endereço (obrigatório)</h5>
        <div class="row">
            <div class="col-12 col-sm-6 col-md-3 mb-3">
                <label class="form-label">CEP <span class="text-danger">*</span></label>
                <input type="text" name="endereco[cep]" value="{{ old('endereco.cep') }}" class="form-control @error('endereco.cep') is-invalid @enderror" required>
                @error('endereco.cep') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-9 col-md-5 mb-3">
                <label class="form-label">Logradouro <span class="text-danger">*</span></label>
                <input type="text" name="endereco[logradouro]" value="{{ old('endereco.logradouro') }}" class="form-control @error('endereco.logradouro') is-invalid @enderror" required>
                @error('endereco.logradouro') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-6 col-sm-3 col-md-2 mb-3">
                <label class="form-label">Número <span class="text-danger">*</span></label>
                <input type="text" name="endereco[numero]" value="{{ old('endereco.numero') }}" class="form-control @error('endereco.numero') is-invalid @enderror" required>
                @error('endereco.numero') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-6 col-sm-6 col-md-2 mb-3">
                <label class="form-label">Complemento</label>
                <input type="text" name="endereco[complemento]" value="{{ old('endereco.complemento') }}" class="form-control @error('endereco.complemento') is-invalid @enderror">
                @error('endereco.complemento') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-6 col-md-4 mb-3">
                <label class="form-label">Bairro <span class="text-danger">*</span></label>
                <input type="text" name="endereco[bairro]" value="{{ old('endereco.bairro') }}" class="form-control @error('endereco.bairro') is-invalid @enderror" required>
                @error('endereco.bairro') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-6 col-md-5 mb-3">
                <label class="form-label">Cidade <span class="text-danger">*</span></label>
                <input type="text" name="endereco[cidade]" value="{{ old('endereco.cidade') }}" class="form-control @error('endereco.cidade') is-invalid @enderror" required>
                @error('endereco.cidade') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-6 col-md-3 mb-3">
                <label class="form-label">Estado (UF) <span class="text-danger">*</span></label>
                <input type="text" name="endereco[estado]" value="{{ old('endereco.estado') }}" class="form-control @error('endereco.estado') is-invalid @enderror" required maxlength="2">
                @error('endereco.estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mt-4">
            <a href="{{ route('professional.patients.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-gradient">Criar Paciente</button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .btn-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; }
</style>
@endpush
