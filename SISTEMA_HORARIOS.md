# Sistema de Horários - Academia

## 📋 Visão Geral

O sistema foi reestruturado para funcionar de forma **inversa**: ao invés de criar horários manualmente, a academia **sempre está aberta** nos horários configurados, e você **bloqueia** apenas quando necessário.

## 🎯 Conceito

### Antes (Sistema Antigo)
- ❌ Precisava criar cada horário manualmente
- ❌ Trabalhoso e propenso a erros
- ❌ Não escalável

### Agora (Sistema Novo)
- ✅ Horários padrão sempre disponíveis
- ✅ Bloqueia apenas quando necessário
- ✅ Configuração centralizada e flexível

## ⚙️ Configuração

### Horários de Funcionamento Padrão

Os horários são configurados através da tabela `settings` com a chave `operating_hours`:

```json
{
  "monday": {
    "start": "06:00",
    "end": "22:00",
    "enabled": true
  },
  "tuesday": {
    "start": "06:00",
    "end": "22:00",
    "enabled": true
  },
  "wednesday": {
    "start": "06:00",
    "end": "22:00",
    "enabled": true
  },
  "thursday": {
    "start": "06:00",
    "end": "22:00",
    "enabled": true
  },
  "friday": {
    "start": "06:00",
    "end": "22:00",
    "enabled": true
  },
  "saturday": {
    "start": "08:00",
    "end": "18:00",
    "enabled": true
  },
  "sunday": {
    "start": "08:00",
    "end": "14:00",
    "enabled": false
  }
}
```

### Outras Configurações

- `slot_duration`: Duração de cada slot em minutos (padrão: 60)
- `default_capacity`: Capacidade padrão por horário (padrão: 20)

## 🚫 Bloqueios de Horários

### Quando Bloquear?

Use bloqueios para:
- 🔧 Manutenção
- 🎉 Feriados
- 🎪 Eventos especiais
- ⛔ Outras necessidades pontuais

### Tabela `schedule_blocks`

Campos:
- `date`: Data do bloqueio
- `start_time`: Hora de início
- `end_time`: Hora de fim
- `reason`: Motivo (maintenance, holiday, event, other)
- `notes`: Observações adicionais
- `created_by`: ID do usuário que criou

### Como Bloquear?

#### Via Service:

```php
use App\Services\ScheduleService;

$scheduleService = app(ScheduleService::class);

// Bloquear um horário
$scheduleService->blockSlot(
    date: '2025-12-25',
    startTime: '06:00',
    endTime: '22:00',
    reason: 'holiday',
    notes: 'Natal - Academia fechada',
    createdBy: auth()->id()
);

// Desbloquear
$scheduleService->unblockSlot($blockId);
```

#### Via Controller (Admin):

Rotas disponíveis:
- `GET /admin/schedule-blocks` - Lista bloqueios
- `GET /admin/schedule-blocks/create` - Formulário de criação
- `POST /admin/schedule-blocks` - Criar bloqueio
- `GET /admin/schedule-blocks/{id}/edit` - Editar bloqueio
- `PUT /admin/schedule-blocks/{id}` - Atualizar bloqueio
- `DELETE /admin/schedule-blocks/{id}` - Remover bloqueio

## 🔧 Service - ScheduleService

### Métodos Disponíveis

#### `getAvailableSlots($date)`
Retorna todos os horários disponíveis para uma data específica (já considerando bloqueios).

```php
$scheduleService = app(ScheduleService::class);
$slots = $scheduleService->getAvailableSlots('2025-11-15');

// Retorna:
[
    [
        'starts_at' => Carbon('2025-11-15 06:00:00'),
        'ends_at' => Carbon('2025-11-15 07:00:00'),
        'capacity' => 20,
        'status' => 'open',
    ],
    // ... mais slots
]
```

#### `isSlotAvailable($datetime)`
Verifica se um horário específico está disponível.

```php
$isAvailable = $scheduleService->isSlotAvailable('2025-11-15 10:00:00');
```

#### `blockSlot(...)`
Bloqueia um horário.

#### `unblockSlot($blockId)`
Remove um bloqueio.

#### `getBlocksForDate($date)`
Retorna todos os bloqueios de uma data.

## 📊 Model - ScheduleBlock

### Métodos Estáticos

```php
// Verificar se está bloqueado
ScheduleBlock::isBlocked('2025-11-15', '10:00:00');

// Buscar bloqueios de uma data
ScheduleBlock::getBlocksForDate('2025-11-15');
```

### Relacionamentos

```php
$block = ScheduleBlock::find(1);
$creator = $block->creator; // Usuário que criou o bloqueio
```

## 🗑️ Tabela `schedules` (Antiga)

A tabela `schedules` ainda existe por compatibilidade, mas **não é mais necessária** para o funcionamento básico. Ela pode ser usada para:
- Sobrescrever capacidade de horários específicos
- Marcar dias como feriado em massa
- Histórico de configurações

## 🚀 Migração de Dados

Se você já tinha horários criados na tabela `schedules`, pode:

1. **Manter**: Continue usando a tabela para referências históricas
2. **Limpar**: Delete todos os registros e use apenas o novo sistema
3. **Migrar**: Converta os dados antigos para bloqueios

```php
// Exemplo: Limpar tabela antiga
DB::table('schedules')->truncate();
```

## 📝 Exemplos Práticos

### Exemplo 1: Bloquear Feriado

```php
// Natal - Academia fechada o dia todo
$scheduleService->blockSlot(
    '2025-12-25',
    '00:00',
    '23:59',
    'holiday',
    'Natal - Academia fechada'
);
```

### Exemplo 2: Bloquear Apenas Parte do Dia

```php
// Manutenção das 14h às 16h
$scheduleService->blockSlot(
    '2025-11-20',
    '14:00',
    '16:00',
    'maintenance',
    'Manutenção do sistema de ar condicionado'
);
```

### Exemplo 3: Buscar Horários Disponíveis

```php
$slots = $scheduleService->getAvailableSlots('2025-11-15');

foreach ($slots as $slot) {
    echo "Horário: {$slot['starts_at']->format('H:i')} - {$slot['ends_at']->format('H:i')}\n";
    echo "Capacidade: {$slot['capacity']}\n";
    echo "---\n";
}
```

## 🎨 Interface Admin (Próximos Passos)

Ainda falta criar as views para o admin gerenciar os bloqueios. Será necessário:

1. ✅ Controller criado (`Admin/ScheduleBlockController`)
2. ⏳ Views pendentes:
   - `admin.schedule-blocks.index` - Lista de bloqueios
   - `admin.schedule-blocks.create` - Formulário de criação
   - `admin.schedule-blocks.edit` - Formulário de edição
   - `admin.schedule-blocks.show` - Visualização
3. ⏳ Rotas pendentes (adicionar em `routes/web.php`)

## 🔄 Próximos Passos

1. Criar as views do admin para gerenciar bloqueios
2. Adicionar rotas no `web.php`
3. Atualizar o sistema de agendamentos para usar o `ScheduleService`
4. Criar testes automatizados
5. Documentar API (se necessário)

## 💡 Dicas

- Configure os horários padrão no seeder `OperatingHoursSeeder`
- Use bloqueios para exceções, não para regras
- Sempre informe o motivo do bloqueio nas notas
- Mantenha histórico de bloqueios para análise
