# Simulação de Uso Semanal - StudioFit

## 📊 Dados Simulados

### Resumo Geral
- **Total de agendamentos**: 37
- **✅ Presenças**: 22
- **❌ Faltas**: 2  
- **🚫 Cancelamentos**: 3
- **📅 Agendados (futuros)**: 10

### Taxa de Presença
- **91.7%** de taxa de presença geral (22 presenças / 24 aulas realizadas)

### Distribuição por Aluno
9 alunos com planos ativos tiveram suas semanas simuladas:

1. **João Santos**: 2 presenças, 1 cancelamento
2. **Ana Costa**: 3 presenças
3. **Carlos Oliveira**: 2 presenças, 1 cancelamento
4. **Juliana Rodrigues**: 2 faltas, 1 cancelamento, +1 crédito extra
5. **Fernanda Lima**: 3 presenças
6. **Ricardo Souza**: 3 presenças, +2 créditos extra
7. **Camila Ferreira**: 3 presenças
8. **Bruno Martins**: 3 presenças
9. **Maria Silva**: 3 presenças, +2 créditos extra

## 💳 Sistema de Créditos

### Logs de Créditos Criados
- **Total de operações**: 22
- **Créditos usados**: 16 (regular + extra)
- **Créditos devolvidos**: 3 (cancelamentos)
- **Créditos extras adicionados**: 3 (reposições administrativas)

### Padrão de Uso Simulado
- Cada aluno fez entre 70% e 100% do seu limite semanal
- 80% de chance de presença
- 10% de chance de falta
- 10% de chance de cancelamento (passado)
- 15% de chance de cancelamento (futuro)
- 30% dos alunos receberam 1-2 créditos extras (reposição)

## 📅 Horários Utilizados
Os agendamentos foram distribuídos pelos horários disponíveis:
- Manhã: 06:00, 07:00, 08:00, 09:00
- Tarde/Noite: 17:00, 18:00, 19:00, 20:00

## 🔄 Como Funciona

### Processo de Simulação

1. **Semana Atual**: Criados agendamentos de segunda a sexta da semana atual
2. **Status Realista**: Agendamentos passados marcados como attended/absent/canceled
3. **Uso de Créditos**: 
   - Créditos regulares usados primeiro
   - Depois créditos extras
   - Cancelamentos devolvem como crédito extra
4. **Próxima Semana**: Alguns agendamentos futuros criados (status: booked)
5. **Créditos Extra**: ~30% dos alunos receberam reposição

### Auditoria Completa
Todas as operações foram registradas em `credit_logs`:
- Tipo de ação (usado, devolvido, adicionado)
- Tipo de crédito (regular ou extra)
- Quantidade
- Saldo após operação
- Motivo da operação
- Relação com booking

## 🎯 Como Rodar Novamente

### Limpar e Recriar
```bash
# Limpar dados existentes
./vendor/bin/sail artisan tinker --execute="
DB::table('bookings')->delete(); 
DB::table('credit_logs')->delete(); 
App\Models\UserPlan::query()->update(['extra_credits' => 0, 'total_credits_used' => 0]);
"

# Executar simulação
./vendor/bin/sail artisan db:seed --class=WeeklyUsageSeeder
```

### Adicionar Agendamentos Futuros
```bash
./vendor/bin/sail artisan tinker --execute="
\$students = App\Models\User::where('role', 'student')->whereHas('activePlan')->limit(5)->get();
\$nextWeek = now()->addWeek()->startOfWeek();
\$times = ['06:00:00', '07:00:00', '18:00:00', '19:00:00'];
foreach (\$students as \$student) {
    for (\$i = 0; \$i < 2; \$i++) {
        \$date = \$nextWeek->copy()->addDays(rand(0, 4));
        \$time = \$times[array_rand(\$times)];
        App\Models\Booking::create([
            'user_id' => \$student->id,
            'date' => \$date->format('Y-m-d'),
            'time' => \$time,
            'status' => 'booked'
        ]);
    }
}
"
```

## 📈 O Que Você Pode Ver Agora

### Dashboard de Alunos (`/admin/students`)
- Lista completa com estatísticas
- Taxa de presença de cada aluno
- Créditos disponíveis (regular + extra)
- Filtros e pesquisa

### Detalhes do Aluno (`/admin/students/{id}`)
- Cards com estatísticas detalhadas
- Histórico completo de agendamentos
- Próximas aulas agendadas
- Log de todas operações de crédito
- Observações do plano

### Dashboard Principal (`/dashboard`)
- Estatísticas gerais atualizadas
- Reservas de hoje
- Gráficos e indicadores

### Calendário (`/bookings`)
- Visualização de todos os horários
- Indicadores de ocupação com ícones de pessoas
- Agendamentos futuros

## 🎨 Dados Realistas

A simulação cria um cenário realista com:
- ✅ **Alta taxa de presença** (80-90%)
- ❌ **Algumas faltas** (10-15%)
- 🚫 **Cancelamentos ocasionais** (5-15%)
- 🎁 **Créditos de reposição** para casos especiais
- 📅 **Agendamentos futuros** já marcados
- 💳 **Uso variado** de créditos regulares e extras

## 📝 Observações

1. **Horários Respeitados**: Apenas seg-sex, horários matinais e noturnos
2. **Limite Semanal**: Respeitado o `classes_per_week` de cada plano
3. **Um Por Dia**: Alunos não fazem 2 aulas no mesmo dia (padrão real)
4. **Logs Completos**: Cada operação tem motivo e responsável
5. **Créditos Inteligentes**: Sistema usa regular primeiro, depois extra
6. **Devolução Como Extra**: Cancelamentos viram créditos de reposição

---

**Seeder**: `database/seeders/WeeklyUsageSeeder.php`
**Executar**: `sail artisan db:seed --class=WeeklyUsageSeeder`
