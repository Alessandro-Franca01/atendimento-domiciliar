@extends('layouts.app')

@section('title', 'Atendimentos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4">Atendimentos</h1>
    <a href="{{ route('atendimentos.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Novo Atendimento</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Paciente</th>
                    <th>Profissional</th>
                    <th>Data</th>
                    <th>Valor</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($atendimentos as $a)
                    <tr>
                        <td>{{ $a->id }}</td>
                        <td>{{ $a->paciente->nome }}</td>
                        <td>{{ $a->profissional->nome }}</td>
                        <td>{{ $a->data_realizacao?->format('d/m/Y H:i') }}</td>
                        <td>R$ {{ number_format($a->valor ?? 0, 2, ',', '.') }}</td>
                        <td>{{ ucfirst($a->status) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">Nenhum atendimento.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $atendimentos->links() }}</div>
 </div>
@endsection