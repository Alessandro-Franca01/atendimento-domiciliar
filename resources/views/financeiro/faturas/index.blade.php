@extends('layouts.app')

@section('title', 'Faturas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4">Faturas</h1>
    <a href="{{ route('faturas.mensal.create') }}" class="btn btn-primary"><i class="fas fa-file-invoice-dollar"></i> Gerar Fatura Mensal</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Paciente</th>
                    <th>Valor</th>
                    <th>Emissão</th>
                    <th>Vencimento</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($faturas as $f)
                    <tr>
                        <td>{{ $f->id }}</td>
                        <td>{{ $f->paciente->nome }}</td>
                        <td>R$ {{ number_format($f->valor_total, 2, ',', '.') }}</td>
                        <td>{{ \Carbon\Carbon::parse($f->data_emissao)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($f->data_vencimento)->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge bg-{{ $f->status === 'aberta' ? 'warning text-dark' : ($f->status === 'paga' ? 'success' : ($f->status === 'vencida' ? 'danger' : 'secondary')) }}">{{ ucfirst($f->status) }}</span>
                        </td>
                        <td>
                            <a href="{{ route('faturas.show', $f) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i> Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">Nenhuma fatura encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $faturas->links() }}
    </div>
 </div>
@endsection