<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserPlan;
use App\Models\Booking;
use App\Models\CreditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WeeklyUsageSeeder extends Seeder
{
    /**
     * Simula uso real da semana para usuários com planos ativos
     * Inclui: presenças, faltas, cancelamentos, reposições
     */
    public function run(): void
    {
        $this->command->info('🎬 Iniciando simulação de uso semanal...');

        // Buscar todos os alunos com plano ativo
        $students = User::where('role', 'student')
            ->whereHas('activePlan')
            ->with('activePlan.plan')
            ->get();

        if ($students->isEmpty()) {
            $this->command->warn('⚠️  Nenhum aluno com plano ativo encontrado.');
            return;
        }

        $this->command->info("📊 Encontrados {$students->count()} alunos com plano ativo");

        // Definir a semana (segunda a domingo)
        $startOfWeek = Carbon::now()->startOfWeek(); // Segunda-feira
        $today = Carbon::now();

        $totalBookings = 0;
        $totalAttended = 0;
        $totalAbsent = 0;
        $totalCancelled = 0;

        foreach ($students as $student) {
            $this->command->newLine();
            $this->command->info("👤 {$student->name}");

            $plan = $student->activePlan;
            $weeklyLimit = $plan->plan->classes_per_week ?? 3; // Fallback para 3 aulas

            // Simular padrão de uso baseado no limite semanal
            $bookingsThisWeek = $this->simulateWeekForStudent(
                $student,
                $plan,
                $startOfWeek,
                $today,
                $weeklyLimit
            );

            $totalBookings += $bookingsThisWeek['total'];
            $totalAttended += $bookingsThisWeek['attended'];
            $totalAbsent += $bookingsThisWeek['absent'];
            $totalCancelled += $bookingsThisWeek['cancelled'];

            $this->command->line("   ✅ {$bookingsThisWeek['attended']} presenças");
            $this->command->line("   ❌ {$bookingsThisWeek['absent']} faltas");
            $this->command->line("   🚫 {$bookingsThisWeek['cancelled']} cancelamentos");
            $this->command->line("   📅 {$bookingsThisWeek['upcoming']} agendamentos futuros");
        }

        $this->command->newLine();
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('📈 RESUMO DA SIMULAÇÃO');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->line("Total de bookings: {$totalBookings}");
        $this->command->line("✅ Presenças: {$totalAttended}");
        $this->command->line("❌ Faltas: {$totalAbsent}");
        $this->command->line("🚫 Cancelamentos: {$totalCancelled}");
        $attendanceRate = $totalBookings > 0 ? round(($totalAttended / ($totalAttended + $totalAbsent)) * 100, 1) : 0;
        $this->command->info("📊 Taxa de presença geral: {$attendanceRate}%");
        $this->command->newLine();
    }

    /**
     * Simula uma semana de uso para um aluno
     */
    private function simulateWeekForStudent(User $student, UserPlan $plan, Carbon $startOfWeek, Carbon $today, int $weeklyLimit): array
    {
        $stats = [
            'total' => 0,
            'attended' => 0,
            'absent' => 0,
            'cancelled' => 0,
            'upcoming' => 0,
        ];

        // Determinar quantas aulas o aluno vai fazer/fez esta semana
        // Varia entre 70% e 100% do limite
        $targetClasses = max(1, rand(ceil($weeklyLimit * 0.7), $weeklyLimit));

        // Horários disponíveis (baseado nas configurações padrão)
        $availableTimes = ['06:00:00', '07:00:00', '08:00:00', '09:00:00', '17:00:00', '18:00:00', '19:00:00', '20:00:00'];

        $bookingsCreated = 0;
        $daysUsed = [];

        // Gerar bookings até atingir o target
        while ($bookingsCreated < $targetClasses) {
            // Escolher dia aleatório da semana (seg-sex)
            $dayOffset = array_rand([0, 1, 2, 3, 4]); // 0=seg, 1=ter, 2=qua, 3=qui, 4=sex
            $bookingDate = $startOfWeek->copy()->addDays($dayOffset);

            // Evitar duplicar dias (alunos geralmente não fazem 2x no mesmo dia)
            if (in_array($bookingDate->format('Y-m-d'), $daysUsed)) {
                continue;
            }

            // Escolher horário aleatório
            $time = $availableTimes[array_rand($availableTimes)];

            // Determinar se já passou
            $bookingDateTime = $bookingDate->copy()->setTimeFromTimeString($time);
            $isPast = $bookingDateTime->lt($today);

            // Se é passado, determinar o status
            if ($isPast) {
                // 80% de chance de presença, 10% falta, 10% cancelamento
                $rand = rand(1, 100);
                if ($rand <= 80) {
                    $status = 'attended';
                    $stats['attended']++;
                } elseif ($rand <= 90) {
                    $status = 'absent';
                    $stats['absent']++;
                } else {
                    $status = 'canceled';
                    $stats['cancelled']++;
                }
            } else {
                // Futuro = booked (ou 15% chance de já estar cancelado)
                if (rand(1, 100) <= 15) {
                    $status = 'canceled';
                    $stats['cancelled']++;
                } else {
                    $status = 'booked';
                    $stats['upcoming']++;
                }
            }

            // Criar o booking
            DB::beginTransaction();
            try {
                $booking = Booking::create([
                    'user_id' => $student->id,
                    'date' => $bookingDate->format('Y-m-d'),
                    'time' => $time,
                    'status' => $status === 'attended' || $status === 'absent' ? 'booked' : $status,
                ]);

                // Processar crédito
                if ($status !== 'canceled') {
                    // Usar crédito (regular primeiro, depois extra)
                    if ($plan->credits_remaining > 0) {
                        $plan->credits_remaining--;
                        $plan->total_credits_used++;
                        $plan->save();
                        
                        CreditLog::logAction(
                            userPlanId: $plan->id,
                            userId: $student->id,
                            createdBy: $student->id,
                            actionType: 'credit_used',
                            creditType: 'regular',
                            amount: 1,
                            balanceAfter: $plan->credits_remaining + $plan->extra_credits,
                            reason: 'Agendamento semanal simulado',
                            bookingId: $booking->id
                        );
                    } elseif ($plan->extra_credits > 0) {
                        $plan->extra_credits--;
                        $plan->total_credits_used++;
                        $plan->save();
                        
                        CreditLog::logAction(
                            userPlanId: $plan->id,
                            userId: $student->id,
                            createdBy: $student->id,
                            actionType: 'credit_used',
                            creditType: 'extra',
                            amount: 1,
                            balanceAfter: $plan->credits_remaining + $plan->extra_credits,
                            reason: 'Agendamento semanal simulado (crédito extra)',
                            bookingId: $booking->id
                        );
                    }
                }

                // Se foi cancelado, devolver como extra
                if ($status === 'canceled') {
                    $plan->extra_credits++;
                    $plan->save();
                    
                    CreditLog::logAction(
                        userPlanId: $plan->id,
                        userId: $student->id,
                        createdBy: $student->id,
                        actionType: 'credit_returned',
                        creditType: 'extra',
                        amount: 1,
                        balanceAfter: $plan->credits_remaining + $plan->extra_credits,
                        reason: 'Cancelamento - devolvido como crédito extra',
                        bookingId: $booking->id
                    );
                }

                // Atualizar status final se foi presença/falta
                if ($status === 'attended' || $status === 'absent') {
                    $booking->update(['status' => $status]);
                }

                DB::commit();

                $stats['total']++;
                $bookingsCreated++;
                $daysUsed[] = $bookingDate->format('Y-m-d');

            } catch (\Exception $e) {
                DB::rollBack();
                $this->command->error("   ❌ Erro ao criar booking: {$e->getMessage()}");
                continue;
            }
        }

        // Adicionar alguns créditos extras para alguns alunos (reposição)
        if (rand(1, 100) <= 30) { // 30% dos alunos ganham crédito extra
            $extraCredits = rand(1, 2);
            $plan->extra_credits += $extraCredits;
            $plan->save();
            
            CreditLog::logAction(
                userPlanId: $plan->id,
                userId: $student->id,
                createdBy: 1, // Admin
                actionType: 'extra_credit_added',
                creditType: 'extra',
                amount: $extraCredits,
                balanceAfter: $plan->credits_remaining + $plan->extra_credits,
                reason: 'Crédito de reposição - simulação'
            );

            $this->command->line("   🎁 +{$extraCredits} crédito(s) extra adicionado(s)");
        }

        return $stats;
    }
}
