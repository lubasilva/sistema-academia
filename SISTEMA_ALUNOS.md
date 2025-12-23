# Sistema de Gerenciamento de Alunos - StudioFit

## 📋 Visão Geral

Sistema completo para gerenciamento de alunos com controle avançado de créditos, incluindo créditos regulares (do plano) e créditos extras (para aulas de reposição).

## ✨ Funcionalidades Implementadas

### 1. **Lista de Alunos** (`/admin/students`)

Interface completa com:
- 🔍 **Pesquisa** por nome ou email
- 🎯 **Filtros** por status (todos, com plano ativo, sem plano)
- 📊 **Estatísticas rápidas**:
  - Total de alunos
  - Alunos com plano ativo
  - Alunos com créditos baixos (≤2)
  - Alunos com créditos extra

#### Tabela de Alunos

Para cada aluno, exibe:
- Nome e email
- Plano ativo (nome + data de término)
- Créditos disponíveis (regular + extra)
- Taxa de presença com indicador visual
- Data da última aula
- Botão para ver detalhes

### 2. **Página de Detalhes do Aluno** (`/admin/students/{id}`)

#### Cards de Estatísticas
- **Créditos Disponíveis**: Total com breakdown (regular + extra)
- **Taxa de Presença**: Porcentagem com total de aulas
- **Faltas**: Número de ausências + cancelamentos
- **Créditos Usados**: Total usado + agendamentos pendentes

#### Seção do Plano Ativo
- Informações completas do plano
- Datas (início/término)
- Breakdown de créditos
- Observações históricas (com data/hora/usuário)
- Botões de ação:
  - ➕ **Adicionar Créditos Extra**
  - 💬 **Adicionar Observação**

#### Histórico de Agendamentos
Tabela completa com:
- Data e horário de cada agendamento
- Status visual (badges coloridos)
- Ações para marcar presença/falta (quando aplicável)
- Paginação

#### Próximas Aulas
Sidebar com lista das próximas 5 aulas agendadas

#### Histórico de Créditos
Log completo de todas as operações:
- Tipo de ação (adicionado, usado, devolvido, etc.)
- Quantidade (+/-)
- Motivo
- Data/hora e usuário responsável
- Saldo após a operação
- Link para booking relacionado (quando aplicável)

### 3. **Modais Interativos**

#### Modal: Adicionar Créditos Extra
- Campo para quantidade (1-100)
- Campo obrigatório de justificativa
- Validação completa
- Explicação sobre regras de uso

#### Modal: Adicionar Observação
- Campo de texto para observação
- Salva automaticamente com timestamp e nome do usuário
- Anexa às observações existentes

### 4. **Ações Administrativas**

#### Marcar Presença/Falta
- Disponível para aulas passadas ainda marcadas como "agendado"
- Botões com ícones intuitivos (✓ presença, ✗ falta)
- Atualiza status instantaneamente

## 🗂️ Estrutura de Arquivos

```
app/
├── Http/Controllers/Admin/
│   └── StudentManagementController.php    # Controller principal
├── Models/
│   ├── User.php                          # Usuário com relação activePlan
│   ├── UserPlan.php                      # Métodos de gestão de créditos
│   ├── CreditLog.php                     # Modelo de auditoria
│   └── Booking.php                       # Agendamentos

resources/views/admin/students/
├── index.blade.php                       # Lista de alunos
└── show.blade.php                        # Detalhes do aluno

routes/
└── web.php                               # Rotas do sistema
```

## 🔧 Rotas Implementadas

```php
// Listagem
GET /admin/students

// Detalhes
GET /admin/students/{student}

// Adicionar créditos extra
POST /admin/students/{student}/extra-credits

// Adicionar observação
POST /admin/students/{student}/observation

// Marcar presença/falta
POST /admin/bookings/{booking}/attendance
```

## 📊 Lógica de Créditos

### Tipos de Créditos

1. **Créditos Regulares** (`credits_remaining`)
   - Vêm do plano contratado
   - Resetam semanalmente
   - Sujeitos a limite semanal
   - Usados primeiro

2. **Créditos Extra** (`extra_credits`)
   - Adicionados manualmente pela administração
   - Sem limite semanal
   - Não expiram com renovação semanal
   - Usados quando créditos regulares acabam
   - Ideal para aulas de reposição

### Fluxo de Uso

1. **Criar Agendamento**:
   - Verifica disponibilidade de créditos (regular + extra)
   - Usa crédito regular primeiro
   - Se não houver regular, usa extra
   - Registra tipo usado no log

2. **Cancelar Agendamento**:
   - Devolve crédito como EXTRA (política de reposição)
   - Permite remarcar sem restrições semanais
   - Registra devolução no log

3. **Adicionar Crédito Extra**:
   - Admin/Instrutor pode adicionar
   - Requer justificativa obrigatória
   - Registra no log com motivo

### Auditoria Completa

Toda operação de crédito gera registro em `credit_logs`:
- `action_type`: Tipo de ação (added, used, returned, etc.)
- `credit_type`: Tipo de crédito (regular ou extra)
- `amount`: Quantidade (+/-)
- `balance_after`: Saldo após operação
- `reason`: Motivo da operação
- `created_by`: Usuário responsável
- `booking_id`: Agendamento relacionado (se houver)

## 🎨 Interface

### Design Responsivo
- Mobile-first com Bootstrap 5
- Cards coloridos por categoria
- Badges para status visual
- Modals para ações
- Tabelas com paginação

### Indicadores Visuais

#### Taxa de Presença
- 🟢 Verde: ≥80%
- 🟡 Amarelo: 60-79%
- 🔴 Vermelho: <60%

#### Créditos
- 🟠 Laranja: ≤2 créditos (aviso)
- 🔵 Azul: >2 créditos (normal)

#### Status de Booking
- 🟢 Verde: Presente
- 🔴 Vermelho: Ausente
- ⚪ Cinza: Cancelado
- 🔵 Azul: Agendado

## 📈 Estatísticas Calculadas

### Por Aluno
- `total_bookings`: Total de agendamentos
- `attended`: Presenças
- `absent`: Faltas
- `cancelled`: Cancelamentos
- `booked`: Agendamentos futuros
- `attendance_rate`: Taxa de presença (%)
- `regular_credits`: Créditos do plano
- `extra_credits`: Créditos extras
- `total_credits`: Soma de ambos
- `used_credits`: Total já utilizado
- `upcoming_classes`: Próximas 5 aulas
- `last_attendance`: Última presença

### Dashboard de Alunos
- Total de alunos cadastrados
- Alunos com plano ativo
- Alunos com créditos baixos
- Alunos com créditos extra

## 🔐 Segurança

- Rotas protegidas por middleware `role:admin`
- Validação de formulários
- Transações de banco de dados
- Auditoria completa de ações
- CSRF protection

## 🚀 Como Usar

### Para Administradores

1. **Visualizar Alunos**:
   - Acesse "Alunos" no menu lateral
   - Use filtros e pesquisa conforme necessário

2. **Ver Detalhes de um Aluno**:
   - Clique no ícone 👁️ na lista
   - Visualize todas as informações

3. **Adicionar Créditos Extra**:
   - Na página de detalhes, clique em "Adicionar Créditos Extra"
   - Informe quantidade e justificativa
   - Confirme

4. **Adicionar Observação**:
   - Na página de detalhes, clique em "Adicionar Observação"
   - Digite a observação
   - Será salva com seu nome e data/hora

5. **Marcar Presença/Falta**:
   - No histórico de agendamentos
   - Clique em ✓ para presença ou ✗ para falta
   - Disponível apenas para aulas passadas

## 📝 Observações Importantes

1. **Créditos Extra vs Regular**:
   - Extra: Para reposição, sem limite semanal
   - Regular: Do plano, com limite semanal

2. **Cancelamentos**:
   - Sempre devolvem como crédito EXTRA
   - Permite reposição flexível

3. **Histórico**:
   - Todas as ações ficam registradas
   - Inclui motivo e responsável
   - Impossível manipular sem rastro

4. **Performance**:
   - Paginação em todas as listas
   - Queries otimizadas com eager loading
   - Cache quando aplicável

## 🔄 Integração com Sistema Existente

O sistema de gerenciamento de alunos se integra perfeitamente com:
- ✅ Sistema de agendamentos
- ✅ Sistema de planos
- ✅ Sistema de horários automático
- ✅ Dashboard administrativo
- ✅ Sistema de autenticação

## 🎯 Próximos Passos (Sugestões)

- [ ] Exportar relatórios em PDF/Excel
- [ ] Gráficos de evolução de presença
- [ ] Notificações por email (créditos baixos, etc.)
- [ ] Envio de mensagens direto pela plataforma
- [ ] Histórico completo de pagamentos integrado
- [ ] Relatório de frequência mensal automático

---

**Sistema desenvolvido com Laravel 12.x**
**Interface com Bootstrap 5 + Bootstrap Icons**
**100% responsivo e mobile-friendly**
