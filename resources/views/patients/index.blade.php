@extends('layouts.app')

@section('title', 'Pacientes - Agendamento Domiciliar')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        <i class="fas fa-users"></i> Pacientes
    </h1>
    <a href="{{ route('patients.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Novo Paciente
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($patients->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum paciente cadastrado</h5>
                <p class="text-muted">Comece criando seu primeiro paciente.</p>
                <a href="{{ route('patients.create') }}" class="btn btn-primary">
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
                            <th>CPF</th>
                            <th>Status</th>
                            <th>Endereços</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patients as $patient)
                            <tr>
                                <td>
                                    <strong>{{ $patient->nome }}</strong>
                                </td>
                                <td>{{ $patient->telefone }}</td>
                                <td>{{ $patient->cpf }}</td>
                                <td>
                                    @if($patient->status == 'ativo')
                                        <span class="badge bg-success">Ativo</span>
                                    @else
                                        <span class="badge bg-secondary">Inativo</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $patient->addresses->count() }}</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('patients.show', $patient) }}" class="btn btn-sm btn-info" title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('patients.edit', $patient) }}" class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('patients.destroy', $patient) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este paciente?')">
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
                {{ $patients->links() }}
            </div>
        @endif
    </div>
</div>
@endsection