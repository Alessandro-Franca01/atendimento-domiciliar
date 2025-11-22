<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Profissional - Agendamento Domiciliar')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.2);
        }

        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }

        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }

        .navbar-professional {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .professional-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .stats-card .icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
        }
    </style>

    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    <div class="container-fluid flex-grow-1">
        <div class="row">
            <!-- Sidebar (desktop) and Offcanvas (mobile) -->
            <div class="col-md-3 col-lg-2 px-0 d-none d-md-block min-vh-100">
                <div class="sidebar p-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white fw-bold">
                            <i class="fas fa-user-md me-2"></i>
                            Profissional
                        </h4>
                        <div class="mt-3">
                            @if(Auth::guard('professional')->user()->foto)
                                <img src="{{ asset('storage/' . Auth::guard('professional')->user()->foto) }}"
                                     alt="{{ Auth::guard('professional')->user()->nome }}"
                                     class="professional-avatar mb-2">
                            @else
                                <div class="professional-avatar bg-light d-inline-flex align-items-center justify-content-center mb-2">
                                    <i class="fas fa-user text-secondary"></i>
                                </div>
                            @endif
                            <div class="text-white-50 small">
                                {{ Auth::guard('professional')->user()->nome }}
                            </div>
                            <div class="text-white-50 small">
                                CREFITO: {{ Auth::guard('professional')->user()->crefito }}
                            </div>
                        </div>
                    </div>

                    <nav class="nav flex-column">
                        <a class="nav-link {{ request()->routeIs('professional.dashboard') ? 'active' : '' }}"
                           href="{{ route('professional.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i>
                            Dashboard
                        </a>

                        <a class="nav-link {{ request()->routeIs('professional.appointments.*') ? 'active' : '' }}"
                           href="{{ route('professional.appointments.index') }}">
                            <i class="fas fa-calendar-alt"></i>
                            Agendamentos
                        </a>

                        <a class="nav-link {{ request()->routeIs('professional.sessions.*') ? 'active' : '' }}"
                           href="{{ route('professional.sessions.index') }}">
                            <i class="fas fa-clipboard-list"></i>
                            Sessões
                        </a>

                        <a class="nav-link {{ request()->routeIs('professional.patients.*') ? 'active' : '' }}"
                           href="{{ route('professional.patients.index') }}">
                            <i class="fas fa-users"></i>
                            Pacientes
                        </a>

                        <a class="nav-link {{ request()->routeIs('professional.financial.*') ? 'active' : '' }}"
                           href="{{ route('professional.financial.dashboard') }}">
                            <i class="fas fa-chart-line"></i>
                            Financeiro
                        </a>

                        <hr class="text-white-50">

                        <a class="nav-link {{ request()->routeIs('professional.profile.*') ? 'active' : '' }}"
                           href="{{ route('professional.profile') }}">
                            <i class="fas fa-user-cog"></i>
                            Meu Perfil
                        </a>

                        <form method="POST" action="{{ route('professional.logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                                <i class="fas fa-sign-out-alt"></i>
                                Sair
                            </button>
                        </form>
                    </nav>
                </div>
            </div>

            <!-- Offcanvas sidebar for small screens -->
            <div class="d-md-none">
                <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="mobileSidebarLabel"><i class="fas fa-user-md me-2"></i> Profissional</h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
                    </div>
                    <div class="offcanvas-body p-0">
                        <div class="sidebar p-3">
                            <div class="text-center mb-3">
                                @if(Auth::guard('professional')->user()->foto)
                                    <img src="{{ asset('storage/' . Auth::guard('professional')->user()->foto) }}"
                                         alt="{{ Auth::guard('professional')->user()->nome }}"
                                         class="professional-avatar mb-2">
                                @else
                                    <div class="professional-avatar bg-light d-inline-flex align-items-center justify-content-center mb-2">
                                        <i class="fas fa-user text-secondary"></i>
                                    </div>
                                @endif
                                <div class="text-white-50 small">
                                    {{ Auth::guard('professional')->user()->nome }}
                                </div>
                                <div class="text-white-50 small">
                                    CREFITO: {{ Auth::guard('professional')->user()->crefito }}
                                </div>
                            </div>
                            <nav class="nav flex-column">
                                <a class="nav-link {{ request()->routeIs('professional.dashboard') ? 'active' : '' }}"
                                   href="{{ route('professional.dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i>
                                    Dashboard
                                </a>
                                <a class="nav-link {{ request()->routeIs('professional.appointments.*') ? 'active' : '' }}"
                                   href="{{ route('professional.appointments.index') }}">
                                    <i class="fas fa-calendar-alt"></i>
                                    Agendamentos
                                </a>
                                <a class="nav-link {{ request()->routeIs('professional.sessions.*') ? 'active' : '' }}"
                                   href="{{ route('professional.sessions.index') }}">
                                    <i class="fas fa-clipboard-list"></i>
                                    Sessões
                                </a>
                                <a class="nav-link {{ request()->routeIs('professional.patients.*') ? 'active' : '' }}"
                                   href="{{ route('professional.patients.index') }}">
                                    <i class="fas fa-users"></i>
                                    Pacientes
                                </a>
                                <a class="nav-link {{ request()->routeIs('professional.financial.*') ? 'active' : '' }}"
                                   href="{{ route('professional.financial.dashboard') }}">
                                    <i class="fas fa-chart-line"></i>
                                    Financeiro
                                </a>
                                <hr class="text-white-50">
                                <a class="nav-link {{ request()->routeIs('professional.profile.*') ? 'active' : '' }}"
                                   href="{{ route('professional.profile') }}">
                                    <i class="fas fa-user-cog"></i>
                                    Meu Perfil
                                </a>
                                <form method="POST" action="{{ route('professional.logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                                        <i class="fas fa-sign-out-alt"></i>
                                        Sair
                                    </button>
                                </form>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Top Bar -->
                <nav class="navbar navbar-professional mb-4">
                    <div class="container-fluid">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div class="d-flex align-items-center">
                                <button class="btn btn-sm btn-outline-secondary d-md-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                                    <i class="fas fa-bars"></i>
                                </button>
                                <h5 class="mb-0">@yield('page-title', 'Dashboard')</h5>
                            </div>
                            <div class="d-flex col-6 align-items-center">
                                <span class="text-muted me-3">
                                    {{ now()->format('d/m/Y') }}
                                </span>
                                <div class="dropdown col-6">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                                        <i class="fas fa-user me-1"></i>
                                        {{ Auth::guard('professional')->user()->nome }}
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('professional.profile') }}">
                                            <i class="fas fa-user-cog me-2"></i>Meu Perfil
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="{{ route('professional.logout') }}">
                                                @csrf
                                                <button type="submit" class="dropdown-item">
                                                    <i class="fas fa-sign-out-alt me-2"></i>Sair
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Content -->
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-light text-center py-3 mt-auto">
        <div class="container">
            <p class="text-muted mb-0">
                &copy; {{ date('Y') }} Agendamento Domiciliar - Sistema de Gestão de Atendimentos
            </p>
        </div>
    </footer>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
