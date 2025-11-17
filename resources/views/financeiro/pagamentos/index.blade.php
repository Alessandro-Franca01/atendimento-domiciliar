@extends('layouts.app')

@section('title', 'Pagamentos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4">Pagamentos</h1>
    <a href="{{ route('pagamentos.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Registrar Pagamento</a>
    
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Paciente</th>
                    <th>Profissional</th>
                    <th>Tipo</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pagamentos as $p)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($p->data_pagamento)->format('d/m/Y') }}</td>
                        <td>{{ $p->paciente->nome }}</td>
                        <td>{{ $p->profissional->nome }}</td>
                        <td>
                            @if($p->sessao_id)
                                <span class="badge bg-info">Sessão</span>
                            @elseif($p->atendimento_id)
                                <span class="badge bg-secondary">Atendimento</span>
                            @else
                                <span class="badge bg-light text-dark">Outro</span>
                            @endif
                        </td>
                        <td>R$ {{ number_format($p->valor, 2, ',', '.') }}</td>
                        <td>
                            @if($p->status === 'pago')
                                <span class="badge bg-success">Pago</span>
                            @elseif($p->status === 'pendente')
                                <span class="badge bg-warning text-dark">Pendente</span>
                            @else
                                <span class="badge bg-danger">Estornado</span>
                            @endif
                        </td>
                        <td>
                            @if($p->status !== 'estornado')
                                <form action="{{ route('pagamentos.estornar', $p) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Confirmar estorno?')">
                                        <i class="fas fa-rotate-left"></i> Estornar
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">Nenhum pagamento registrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $pagamentos->links() }}
    </div>
 </div>
@endsection