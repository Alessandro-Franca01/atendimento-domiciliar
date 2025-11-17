<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Agendamento Domiciliar')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-home"></i> Agendamento Domiciliar
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="pacientesDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-users"></i> Pacientes
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('pacientes.index') }}">Listar Pacientes</a></li>
                            <li><a class="dropdown-item" href="{{ route('pacientes.create') }}">Novo Paciente</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="sessoesDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-calendar-check"></i> Sessões
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('sessoes.index') }}">Listar Sessões</a></li>
                            <li><a class="dropdown-item" href="{{ route('sessoes.create') }}">Nova Sessão</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('agendamentos.index') }}">
                            <i class="fas fa-calendar-alt"></i> Agendamentos
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profissionaisDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-md"></i> Profissionais
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('profissionals.index') }}">Listar Profissionais</a></li>
                            <li><a class="dropdown-item" href="{{ route('profissionals.create') }}">Novo Profissional</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="financeiroDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-hand-holding-dollar"></i> Financeiro
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('pagamentos.index') }}">Pagamentos</a></li>
                            <li><a class="dropdown-item" href="{{ route('pagamentos.create') }}">Registrar Pagamento</a></li>
                            <li><a class="dropdown-item" href="{{ route('faturas.index') }}">Faturas</a></li>
                            <li><a class="dropdown-item" href="{{ route('faturas.mensal.create') }}">Gerar Fatura Mensal</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('financeiro.fluxo-caixa') }}">Fluxo de Caixa</a></li>
                            <li><a class="dropdown-item" href="{{ route('financeiro.relatorio-sessoes') }}">Relatório de Sessões</a></li>
                            <li><a class="dropdown-item" href="{{ route('financeiro.relatorio-pacientes') }}">Relatório de Pacientes</a></li>
                            <li><a class="dropdown-item" href="{{ route('financeiro.relatorio-geral') }}">Relatório Geral</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle"></i> {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-light text-center py-3 mt-5">
        <div class="container">
            <p class="text-muted mb-0">
                &copy; {{ date('Y') }} Agendamento Domiciliar - Sistema de Gestão de Atendimentos
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>