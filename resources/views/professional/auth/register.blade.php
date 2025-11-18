<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cadastro Profissional - Agendamento Domiciliar</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .register-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 1000px;
            width: 100%;
            margin: 0 auto;
        }
        
        .register-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .register-header i {
            font-size: 60px;
            margin-bottom: 15px;
        }
        
        .register-form {
            padding: 40px;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .btn-back {
            background: transparent;
            border: 2px solid #667eea;
            border-radius: 10px;
            padding: 12px 30px;
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-back:hover {
            background: #667eea;
            color: white;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
        }
        
        @media (max-width: 768px) {
            .register-form {
                padding: 30px 20px;
            }
            
            .register-header {
                padding: 20px;
            }
            
            .register-header i {
                font-size: 50px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <i class="fas fa-user-md mb-3"></i>
            <h3 class="fw-bold">Cadastro de Profissional</h3>
            <p class="mb-0">Preencha os dados para criar sua conta</p>
        </div>
        
        <div class="register-form">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Erro no cadastro:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            <form method="POST" action="{{ route('professional.register') }}">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-user me-2"></i>
                            Dados Pessoais
                        </h5>
                        
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome Completo *</label>
                            <input type="text" 
                                   class="form-control @error('nome') is-invalid @enderror" 
                                   id="nome" 
                                   name="nome" 
                                   value="{{ old('nome') }}"
                                   required>
                            @error('nome')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail *</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="cpf" class="form-label">CPF *</label>
                            <input type="text" 
                                   class="form-control @error('cpf') is-invalid @enderror" 
                                   id="cpf" 
                                   name="cpf" 
                                   value="{{ old('cpf') }}"
                                   required
                                   placeholder="000.000.000-00">
                            @error('cpf')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                            <input type="date" 
                                   class="form-control @error('data_nascimento') is-invalid @enderror" 
                                   id="data_nascimento" 
                                   name="data_nascimento" 
                                   value="{{ old('data_nascimento') }}">
                            @error('data_nascimento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="telefone" class="form-label">Telefone *</label>
                            <input type="text" 
                                   class="form-control @error('telefone') is-invalid @enderror" 
                                   id="telefone" 
                                   name="telefone" 
                                   value="{{ old('telefone') }}"
                                   required
                                   placeholder="(00) 00000-0000">
                            @error('telefone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-briefcase me-2"></i>
                            Dados Profissionais
                        </h5>
                        
                        <div class="mb-3">
                            <label for="crefito" class="form-label">CREFITO *</label>
                            <input type="text" 
                                   class="form-control @error('crefito') is-invalid @enderror" 
                                   id="crefito" 
                                   name="crefito" 
                                   value="{{ old('crefito') }}"
                                   required
                                   placeholder="000000-F">
                            @error('crefito')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="especialidades" class="form-label">Especialidades</label>
                            <textarea class="form-control @error('especialidades') is-invalid @enderror" 
                                      id="especialidades" 
                                      name="especialidades" 
                                      rows="3"
                                      placeholder="Fisioterapia Ortopédica, Neurologia, etc.">{{ old('especialidades') }}</textarea>
                            @error('especialidades')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="horario_funcionamento" class="form-label">Horário de Funcionamento</label>
                            <input type="text" 
                                   class="form-control @error('horario_funcionamento') is-invalid @enderror" 
                                   id="horario_funcionamento" 
                                   name="horario_funcionamento" 
                                   value="{{ old('horario_funcionamento') }}"
                                   placeholder="Segunda a Sexta, 8h às 18h">
                            @error('horario_funcionamento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Senha *</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password"
                                   required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Mínimo 8 caracteres</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmar Senha *</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation"
                                   required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="sobre" class="form-label">Sobre Mim</label>
                            <textarea class="form-control @error('sobre') is-invalid @enderror" 
                                      id="sobre" 
                                      name="sobre" 
                                      rows="4"
                                      placeholder="Conte um pouco sobre sua experiência profissional...">{{ old('sobre') }}</textarea>
                            @error('sobre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('professional.login') }}" class="btn btn-back">
                        <i class="fas fa-arrow-left me-2"></i>
                        Voltar ao Login
                    </a>
                    
                    <button type="submit" class="btn btn-register">
                        <i class="fas fa-user-plus me-2"></i>
                        Criar Conta
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Máscaras para CPF e telefone
        document.getElementById('cpf').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            }
            e.target.value = value;
        });
        
        document.getElementById('telefone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
            }
            e.target.value = value;
        });
    </script>
</body>
</html>