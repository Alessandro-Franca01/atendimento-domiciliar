# Sistema de Agendamento de Atendimentos Domiciliares

Sistema completo para gestão de atendimentos domiciliares de fisioterapia, desenvolvido com Laravel 11 e Blade.

## 🎯 Objetivo

Sistema focado em atendimentos domiciliares de fisioterapia, onde o próprio profissional gerencia:
- Pacientes
- Vários endereços por paciente
- Sessões de atendimento (pacotes)
- Horários fixos (recorrentes)
- Agendamentos
- Atendimentos realizados

## 🏗️ Arquitetura do Sistema

### Entidades Principais

1. **Profissional**: Fisioterapeuta responsável pelos atendimentos
2. **Paciente**: Cliente atendido pelo profissional
3. **Endereço**: Múltiplos endereços por paciente (casa, trabalho, familiar)
4. **Sessão**: Pacote/contrato de sessões de atendimento
5. **SessaoHorario**: Horários fixos e recorrentes da sessão
6. **Agendamento**: Compromissos marcados
7. **Atendimento**: Registro clínico da visita realizada

### Relacionamentos

- Paciente 1 --- N Endereços
- Paciente 1 --- N Sessões
- Profissional 1 --- N Sessões
- Sessão 1 --- N SessaoHorario
- Sessão 1 --- N Agendamentos
- SessaoHorario 1 --- N Agendamentos (opcional)
- Agendamento 1 --- 1 Atendimento

## 🚀 Funcionalidades

### Gestão de Pacientes
- Cadastro completo com dados pessoais
- Múltiplos endereços por paciente
- Status ativo/inativo
- Observações e anotações

### Gestão de Sessões
- Criação de pacotes de sessões
- Definição de quantidade total de sessões
- Acompanhamento de sessões realizadas
- Status: ativo, concluído, suspenso

### Horários Fixos (Recorrentes)
- Definição de horários semanais fixos
- Dias da semana configuráveis
- Duração personalizada por horário
- Vinculação a endereços específicos
- Ativação/desativação de horários

### Agendamentos
- Geração automática baseada em horários fixos
- Agendamentos avulsos manuais
- Verificação de disponibilidade do profissional
- Status: agendado, confirmado, cancelado, concluído, faltou

### Atendimentos
- Registro clínico com evolução
- Procedimentos realizados
- Assinatura digital do paciente
- Atualização automática do progresso da sessão

### Dashboard
- Visão geral do sistema
- Total de pacientes, sessões e agendamentos
- Próximos agendamentos
- Sessões próximas de terminar

## 📋 Requisitos

- PHP 8.1 ou superior
- Composer
- MySQL/SQLite
- Node.js (opcional, para assets)

## 🔧 Instalação

1. **Clone o repositório**
```bash
git clone [url-do-repositorio]
cd agendamento-domiciliar
```

2. **Instale as dependências**
```bash
composer install
```

3. **Configure o ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure o banco de dados**
Edite o arquivo `.env` com suas credenciais do banco de dados:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agendamento_domiciliar
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

5. **Execute as migrações**
```bash
php artisan migrate
```

6. **Inicie o servidor de desenvolvimento**
```bash
php artisan serve
```

Acesse o sistema em: `http://localhost:8000`

## 🎯 Como Usar

### 1. Cadastrar Profissional
- Acesse o menu "Profissionais" → "Novo Profissional"
- Preencha os dados: nome, CREFITO, telefone, especialidades

### 2. Cadastrar Paciente
- Acesse o menu "Pacientes" → "Novo Paciente"
- Preencha os dados pessoais
- Adicione os endereços de atendimento

### 3. Criar Sessão
- Acesse o menu "Sessões" → "Nova Sessão"
- Selecione o paciente e profissional
- Defina a descrição e quantidade total de sessões

### 4. Definir Horários Fixos
- Na página da sessão, adicione horários fixos
- Configure: dia da semana, horário, duração, endereço

### 5. Gerar Agendamentos
- O sistema gera automaticamente agendamentos baseados nos horários fixos
- Use o comando: `php artisan agendamentos:gerar-automaticos`
- Ou gere manualmente na página da sessão

### 6. Realizar Atendimento
- Acesse o agendamento
- Registre a evolução e procedimentos realizados
- O sistema atualiza automaticamente o progresso da sessão

## ⚙️ Comandos Artisan

### Gerar Agendamentos Automáticos
```bash
# Gerar para os próximos 30 dias (padrão)
php artisan agendamentos:gerar-automaticos

# Gerar para um período específico
php artisan agendamentos:gerar-automaticos --dias=60
```

### Agendamento Automático (Cron)
O sistema está configurado para gerar agendamentos automaticamente todos os dias às 6h da manhã.

Configure o cron no servidor:
```bash
# Adicione esta linha ao crontab
0 6 * * * cd /caminho/para/seu/projeto && php artisan agendamentos:gerar-automaticos >> /dev/null 2>&1
```

## 🔒 Segurança

- Validação de dados em todos os formulários
- Proteção contra SQL injection via Eloquent ORM
- CSRF protection habilitada
- Validação de permissões e regras de negócio

## 📊 Regras de Negócio Implementadas

1. **Sessões**: Devem ter quantidade total definida e sessões realizadas aumentam automaticamente
2. **Horários Fixos**: Podem ser desativados e geram agendamentos recorrentes
3. **Agendamentos**: Validam disponibilidade do profissional e status da sessão
4. **Atendimentos**: Ao concluir, incrementam sessões realizadas e finalizam sessão quando completa
5. **Exclusões**: Previne exclusão de entidades com relacionamentos ativos

## 🎨 Interface

- Bootstrap 5 para design responsivo
- Font Awesome para ícones
- Interface intuitiva e amigável
- Notificações de sucesso/erro
- Confirmações antes de ações destrutivas

## 📁 Estrutura de Pastas

```
app/
├── Console/Commands/          # Comandos Artisan
├── Http/Controllers/           # Controladores
├── Models/                     # Modelos Eloquent
├── Services/                   # Serviços de negócio
└── ...

resources/views/               # Views Blade
├── layouts/                    # Layouts principais
├── pacientes/                  # Views de pacientes
├── sessoes/                    # Views de sessões
└── ...

database/
├── migrations/                 # Migrações do banco
└── ...
```

## 🤝 Contribuindo

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 📞 Suporte

Para suporte, entre em contato através do email: [seu-email@exemplo.com]

---

Desenvolvido com ❤️ para fisioterapeutas que atendem domiciliarmente.