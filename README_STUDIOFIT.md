# StudioFit - Sistema de Gestão de Academia/Estúdio

Sistema completo para gestão de academias e estúdios desenvolvido com Laravel 11, Bootstrap 5 e integrações completas.

## 🚀 Tecnologias

- **Backend:** Laravel 11 + PHP 8.2
- **Database:** MySQL 8
- **Frontend:** Bootstrap 5 + Bootstrap Icons + FullCalendar
- **Integrações:** Asaas (Pagamentos) + Telegram (Notificações)
- **Ambiente:** Laravel Sail (Docker)

## ✨ Funcionalidades

### Autenticação e Autorização
- ✅ Login, registro e recuperação de senha
- ✅ Políticas de acesso por papéis (Admin, Instrutor, Aluno)
- ✅ Gates para verificações de permissões

### Agenda Visual
- ✅ Calendário interativo com FullCalendar
- ✅ Reservas com validação de créditos
- ✅ Validação de capacidade máxima por aula
- ✅ Validação de antecedência mínima
- ✅ Cancelamento com devolução de créditos

### Planos e Pagamentos
- ✅ Múltiplos planos com créditos e validade
- ✅ Integração completa com Asaas
- ✅ Pagamento via PIX (com QR Code)
- ✅ Pagamento via Boleto
- ✅ Webhooks para confirmação automática
- ✅ Ativação/renovação automática de planos

### Notificações Telegram
- ✅ Alertas de nova reserva
- ✅ Alertas de cancelamento
- ✅ Lembretes de aula (agendáveis)
- ✅ Confirmação de pagamentos
- ✅ Avisos de planos expirando
- ✅ Broadcast para comunicados

### Dashboards
- ✅ Dashboard administrativo com gráficos
- ✅ Dashboard do instrutor
- ✅ Dashboard do aluno
- ✅ Gráficos de reservas e receita (Chart.js)
- ✅ Estatísticas em tempo real

### Auditoria
- ✅ Logs de todas ações importantes
- ✅ Histórico completo de atividades

## 📋 Requisitos

- Docker e Docker Compose
- Git
- Conta Asaas (para pagamentos)
- Bot Telegram (para notificações)

## 🔧 Instalação

### 1. Clonar o repositório

```bash
git clone <seu-repositorio>
cd academia
```

### 2. Copiar e configurar .env

```bash
cp .env.example .env
```

Edite o `.env` e configure:

```env
APP_NAME=StudioFit
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=America/Sao_Paulo
APP_LOCALE=pt_BR
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=studiofit
DB_USERNAME=sail
DB_PASSWORD=password

# Asaas
ASAAS_API_KEY=sua_chave_api
ASAAS_BASE_URL=https://sandbox.asaas.com/api/v3
ASAAS_WEBHOOK_TOKEN=seu_token_webhook

# Telegram
TELEGRAM_BOT_TOKEN=seu_bot_token
```

### 3. Instalar dependências e iniciar

```bash
# Instalar dependências
./vendor/bin/sail composer install
./vendor/bin/sail npm install

# Gerar chave da aplicação
./vendor/bin/sail artisan key:generate

# Rodar migrations e seeders
./vendor/bin/sail artisan migrate:fresh --seed

# Compilar assets
./vendor/bin/sail npm run build

# Iniciar servidor
./vendor/bin/sail up -d
```

O sistema estará disponível em: `http://localhost`

## 👤 Usuários Padrão

Após rodar os seeders, os seguintes usuários estarão disponíveis:

**Admin:**
- Email: admin@studiofit.com
- Senha: password

**Instrutor:**
- Email: instrutor@studiofit.com
- Senha: password

**Aluno:**
- Email: aluno@studiofit.com
- Senha: password

## 📱 Estrutura de Papéis

### Admin
- Acesso completo ao sistema
- Gerenciamento de usuários
- Gerenciamento de planos
- Gerenciamento de horários
- Visualização de todos pagamentos
- Relatórios completos
- Configurações do sistema

### Instrutor
- Visualização de aulas agendadas
- Marcação de presenças
- Visualização de alunos
- Estatísticas de aulas

### Aluno
- Visualização de agenda
- Criação de reservas
- Cancelamento de reservas
- Visualização do plano ativo
- Assinatura de planos
- Histórico de pagamentos

## 🔗 Rotas Principais

```
GET  /                      - Página inicial
GET  /login                 - Login
GET  /register              - Registro
GET  /dashboard             - Dashboard (por papel)
GET  /bookings              - Agenda de aulas
POST /bookings              - Criar reserva
DELETE /bookings/{id}       - Cancelar reserva
GET  /payments              - Meus pagamentos
GET  /payments/create       - Escolher plano
POST /payments              - Criar pagamento
GET  /payments/{id}         - Detalhes do pagamento (PIX)
POST /api/webhooks/asaas    - Webhook Asaas
```

## ⚙️ Configurações

As configurações do sistema estão na tabela `settings`:

- `max_capacity_per_class` - Capacidade máxima por aula (padrão: 10)
- `min_booking_hours` - Antecedência mínima para reserva em horas (padrão: 2)
- `min_cancel_hours` - Antecedência mínima para cancelamento em horas (padrão: 2)
- `booking_start_time` - Horário de início (padrão: 06:00)
- `booking_end_time` - Horário de término (padrão: 22:00)
- `slot_duration_minutes` - Duração do slot em minutos (padrão: 60)

## 🔔 Comandos Artisan

### Enviar lembretes de aulas

```bash
./vendor/bin/sail artisan reminders:classes --hours=2
```

Agende no cron para rodar a cada hora:

```bash
0 * * * * cd /path/to/project && ./vendor/bin/sail artisan reminders:classes --hours=2
```

## 🧪 Testes

Execute os testes:

```bash
./vendor/bin/sail artisan test
```

Testes disponíveis:
- Autenticação (login, logout, registro)
- Reservas (criação, cancelamento, validações)
- Políticas de acesso
- Integração com Asaas (via mocks)

## 🔒 Segurança

- CSRF Protection habilitado
- Validação de webhooks Asaas por token
- Políticas de acesso rigorosas
- Senhas hasheadas com bcrypt
- Logs de auditoria completos

## 📊 Banco de Dados

### Principais Tabelas

- `users` - Usuários do sistema
- `plans` - Planos disponíveis
- `user_plans` - Planos ativos dos usuários
- `schedules` - Horários disponíveis
- `bookings` - Reservas
- `payments` - Pagamentos
- `attendances` - Presenças
- `audit_logs` - Logs de auditoria
- `settings` - Configurações do sistema

## 🚀 Deploy

### Makefile

O projeto inclui um Makefile com comandos úteis:

```bash
make up          # Iniciar containers
make down        # Parar containers
make rebuild     # Reconstruir containers
make migrate     # Executar migrations
make seed        # Executar seeders
make test        # Executar testes
```

## 📝 API Asaas

### Webhook

Configure no painel Asaas o webhook apontando para:

```
POST https://seu-dominio.com/api/webhooks/asaas
```

Token de autenticação: configure `ASAAS_WEBHOOK_TOKEN` no `.env`

### Eventos tratados

- `PAYMENT_RECEIVED` - Pagamento recebido
- `PAYMENT_CONFIRMED` - Pagamento confirmado
- `PAYMENT_OVERDUE` - Pagamento vencido
- `PAYMENT_DELETED` - Pagamento cancelado
- `PAYMENT_REFUNDED` - Pagamento estornado

## 🤖 Bot Telegram

### Configuração

1. Crie um bot com @BotFather no Telegram
2. Obtenha o token do bot
3. Configure `TELEGRAM_BOT_TOKEN` no `.env`
4. Usuários devem adicionar o bot e enviar `/start`
5. Salve o `chat_id` do usuário no campo `telegram_chat_id`

### Notificações automáticas

- Nova reserva
- Cancelamento de reserva
- Lembrete de aula (2h antes)
- Pagamento confirmado
- Pagamento vencido
- Plano expirando

## 📈 Melhorias Futuras

- [ ] App mobile (Flutter/React Native)
- [ ] Integração com mais gateways de pagamento
- [ ] Sistema de avaliações
- [ ] Gamificação (badges, conquistas)
- [ ] Relatórios em PDF
- [ ] Integração com WhatsApp
- [ ] Sistema de filas (Redis)
- [ ] Cache (Redis)

## 📄 Licença

Este projeto é proprietário.

## 👨‍💻 Desenvolvedor

Sistema desenvolvido para gerenciamento completo de academias e estúdios.

---

**StudioFit** - Gestão inteligente para seu negócio fitness 💪
