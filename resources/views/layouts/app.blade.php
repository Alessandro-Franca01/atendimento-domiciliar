<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Agendamento Domiciliar')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.9);
            border-radius: 6px;
            margin: 3px 0;
        }
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.08);
            color: #fff;
            transform: translateX(4px);
        }
        .sidebar .nav-link i { width: 20px; margin-right: 8px; }
        .main-content { background: #f8f9fa; min-height: 100vh; }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <!-- Mobile offcanvas toggle -->
            <button class="btn btn-sm btn-outline-light d-md-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                <i class="fas fa-bars"></i>
            </button>
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
                        <a class="nav-link dropdown-toggle" href="#" id="patientsDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-users"></i> Pacientes
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('patients.index') }}">Listar Pacientes</a></li>
                            <li><a class="dropdown-item" href="{{ route('patients.create') }}">Novo Paciente</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="sessionsDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-calendar-check"></i> Sessões
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('therapy-sessions.index') }}">Listar Sessões</a></li>
                            <li><a class="dropdown-item" href="{{ route('therapy-sessions.create') }}">Nova Sessão</a></li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('appointments.index') }}">
                            <i class="fas fa-calendar-alt"></i> Agendamentos
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('attendances.index') }}">
                            <i class="fas fa-file-medical"></i> Atendimentos
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="financialDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-hand-holding-dollar"></i> Financeiro
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('payments.index') }}">Pagamentos</a></li>
                            <li><a class="dropdown-item" href="{{ route('payments.create') }}">Registrar Pagamento</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('invoices.index') }}">Faturas</a></li>
                            <li><a class="dropdown-item" href="{{ route('invoices.monthly.create') }}">Gerar Fatura Mensal</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('financial.cash-flow') }}">Fluxo de Caixa</a></li>
                            <li><a class="dropdown-item" href="{{ route('financial.sessions-report') }}">Relatório de Sessões</a></li>
                            <li><a class="dropdown-item" href="{{ route('financial.patients-report') }}">Relatório de Pacientes</a></li>
                            <li><a class="dropdown-item" href="{{ route('financial.general-report') }}">Relatório Geral</a></li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('professional.login') }}">
                            <i class="fas fa-user-md"></i> Área Profissional
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Offcanvas sidebar (mobile) -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="mobileSidebarLabel"><i class="fas fa-bars me-2"></i> Menu</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div class="sidebar p-3">
                <nav class="nav flex-column">
                    <a class="nav-link" href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <a class="nav-link" href="{{ route('patients.index') }}"><i class="fas fa-users"></i> Pacientes</a>
                    <a class="nav-link" href="{{ route('therapy-sessions.index') }}"><i class="fas fa-calendar-check"></i> Sessões</a>
                    <a class="nav-link" href="{{ route('appointments.index') }}"><i class="fas fa-calendar-alt"></i> Agendamentos</a>
                    <a class="nav-link" href="{{ route('attendances.index') }}"><i class="fas fa-file-medical"></i> Atendimentos</a>
                    <a class="nav-link" href="{{ route('payments.index') }}"><i class="fas fa-hand-holding-dollar"></i> Financeiro</a>
                    <a class="nav-link" href="{{ route('professional.login') }}"><i class="fas fa-user-md"></i> Área Profissional</a>
                </nav>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Desktop sidebar -->
            <div class="col-md-3 col-lg-2 d-none d-md-block px-0">
                <div class="sidebar p-3">
                    <nav class="nav flex-column">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        <a class="nav-link {{ request()->routeIs('patients.*') ? 'active' : '' }}" href="{{ route('patients.index') }}"><i class="fas fa-users"></i> Pacientes</a>
                        <a class="nav-link {{ request()->routeIs('therapy-sessions.*') ? 'active' : '' }}" href="{{ route('therapy-sessions.index') }}"><i class="fas fa-calendar-check"></i> Sessões</a>
                        <a class="nav-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}" href="{{ route('appointments.index') }}"><i class="fas fa-calendar-alt"></i> Agendamentos</a>
                        <a class="nav-link {{ request()->routeIs('attendances.*') ? 'active' : '' }}" href="{{ route('attendances.index') }}"><i class="fas fa-file-medical"></i> Atendimentos</a>
                        <a class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}" href="{{ route('payments.index') }}"><i class="fas fa-hand-holding-dollar"></i> Financeiro</a>
                        <hr class="text-white-50">
                        <a class="nav-link" href="{{ route('professional.login') }}"><i class="fas fa-user-md"></i> Área Profissional</a>
                    </nav>
                </div>
            </div>
        </div>
        <main class="col-12 col-md-9 col-lg-10 main-content">
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
    </div>

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
