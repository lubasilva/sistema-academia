# 🔄 Guia Rápido - Nova Interface de Horários

## O que mudou?

### ✅ Antes
- Você precisava criar cada horário manualmente
- Era trabalhoso e repetitivo

### ✅ Agora
- **Horários sempre liberados** automaticamente (seg-sex 6h-22h, sáb 8h-18h)
- Você só **bloqueia** quando necessário (feriados, manutenção, eventos)

## 📱 Nova Interface

Acesse: **Menu > Horários** (`/schedules`)

### 3 Abas Principais:

#### 1️⃣ Horários de Funcionamento
- Visualize os horários padrão da semana
- Veja quais dias estão abertos
- (Em breve: editar diretamente)

#### 2️⃣ Bloqueios
- Lista de todos os bloqueios ativos
- Criar novo bloqueio (botão azul)
- Editar/remover bloqueios existentes

#### 3️⃣ Configurações
- Duração dos slots (padrão: 60min)
- Capacidade padrão (padrão: 20 pessoas)

## 🚫 Como Bloquear Horários

### Opção 1: Pela Interface
1. Acesse a aba **Bloqueios**
2. Clique em **"Novo Bloqueio"**
3. Preencha:
   - Data
   - Horário início/fim
   - Motivo (Manutenção, Feriado, Evento, Outro)
   - Observações (opcional)
4. Clique em **"Criar Bloqueio"**

### Opção 2: Via Código/Tinker
```php
use App\Services\ScheduleService;

$service = app(ScheduleService::class);

// Bloquear Natal (dia todo)
$service->blockSlot(
    date: '2025-12-25',
    startTime: '00:00',
    endTime: '23:59',
    reason: 'holiday',
    notes: 'Natal - Academia fechada'
);

// Bloquear apenas parte do dia
$service->blockSlot(
    date: '2025-11-20',
    startTime: '14:00',
    endTime: '16:00',
    reason: 'maintenance',
    notes: 'Manutenção do ar condicionado'
);
```

## 🎯 Casos de Uso Comuns

### Feriado Nacional
```
Data: 2025-12-25
Hora: 00:00 - 23:59
Motivo: Feriado
Obs: Natal - Academia fechada
```

### Manutenção Parcial
```
Data: 2025-11-15
Hora: 14:00 - 16:00
Motivo: Manutenção
Obs: Troca de equipamentos
```

### Evento Especial
```
Data: 2025-12-31
Hora: 18:00 - 23:59
Motivo: Evento
Obs: Festa de confraternização
```

## 🔧 Configurações Atuais

Execute para ver as configurações:
```bash
./vendor/bin/sail artisan tinker
>>> Setting::get('operating_hours')
>>> Setting::get('slot_duration')
>>> Setting::get('default_capacity')
```

## ⚠️ Importante

1. **Bloqueios não cancelam reservas existentes** - você precisa cancelar manualmente
2. **Horários são gerados automaticamente** - não precisa criar manualmente
3. **A tabela `schedules` antiga** ainda existe mas não é mais necessária

## 🗑️ Limpeza (Opcional)

Se quiser limpar os horários antigos da tabela `schedules`:

```bash
./vendor/bin/sail artisan tinker
>>> DB::table('schedules')->truncate();
```

**Atenção**: Isso apagará TODOS os registros antigos!

## 📊 Status Atual

✅ Backend completo e funcionando
✅ Interface de gerenciamento criada
✅ Sistema de bloqueios implementado
✅ API atualizada para usar novo sistema
⏳ Edição de horários padrão (em breve)
⏳ Edição de configurações pela interface (em breve)

## 🆘 Suporte

Dúvidas? Verifique:
- `SISTEMA_HORARIOS.md` - Documentação técnica completa
- Código fonte em `app/Services/ScheduleService.php`
- Views em `resources/views/admin/schedules/`
