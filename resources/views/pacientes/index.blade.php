@extends('layouts.app')

@section('title', 'Pacientes - Agendamento Domiciliar')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        <i class="fas fa-users"></i> Pacientes
    </h1>
    <a href="{{ route('pacientes.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Novo Paciente
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($pacientes->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum paciente cadastrado</h5>
                <p class="text-muted">Comece criando seu primeiro paciente.</p>
                <a href="{{ route('pacientes.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Criar Primeiro Paciente
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Telefone</th>
                            <th>Documento</th>
                            <th>Status</th>
                            <th>Endereços</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pacientes as $paciente)
                            <tr>
                                <td>
                                    <strong>{{ $paciente->nome }}</strong>
                                </td>
                                <td>{{ $paciente->telefone }}</td>
                                <td>{{ $paciente->documento }}</td>
                                <td>
                                    @if($paciente->status == 'ativo')
                                        <span class="badge bg-success">Ativo</span>
                                    @else
                                        <span class="badge bg-secondary">Inativo</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $paciente->enderecos->count() }}</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('pacientes.show', $paciente) }}" class="btn btn-sm btn-info" title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('pacientes.edit', $paciente) }}" class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('pacientes.destroy', $paciente) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este paciente?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center">
                {{ $pacientes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection