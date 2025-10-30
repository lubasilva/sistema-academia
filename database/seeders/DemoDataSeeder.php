<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Plan;
use App\Models\UserPlan;
use App\Models\Payment;
use App\Models\Schedule;
use App\Models\Booking;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar planos existentes
        $plans = Plan::all();
        
        if ($plans->isEmpty()) {
            $this->command->error('Nenhum plano encontrado! Execute primeiro o PlanSeeder.');
            return;
        }

        // Criar 10 alunos com planos ativos
        $students = [
            [
                'name' => 'Maria Silva',
                'email' => 'maria.silva@email.com',
                'phone' => '11987654321',
                'cpf' => '12345678901',
                'plan' => $plans->where('billing_cycle', 'monthly')->first(),
                'started_days_ago' => 15,
            ],
            [
                'name' => 'João Santos',
                'email' => 'joao.santos@email.com',
                'phone' => '11987654322',
                'cpf' => '23456789012',
                'plan' => $plans->where('billing_cycle', 'quarterly')->first(),
                'started_days_ago' => 30,
            ],
            [
                'name' => 'Ana Costa',
                'email' => 'ana.costa@email.com',
                'phone' => '11987654323',
                'cpf' => '34567890123',
                'plan' => $plans->where('billing_cycle', 'monthly')->first(),
                'started_days_ago' => 5,
            ],
            [
                'name' => 'Carlos Oliveira',
                'email' => 'carlos.oliveira@email.com',
                'phone' => '11987654324',
                'cpf' => '45678901234',
                'plan' => $plans->where('billing_cycle', 'semiannual')->first(),
                'started_days_ago' => 60,
            ],
            [
                'name' => 'Juliana Rodrigues',
                'email' => 'juliana.rodrigues@email.com',
                'phone' => '11987654325',
                'cpf' => '56789012345',
                'plan' => $plans->where('billing_cycle', 'quarterly')->first(),
                'started_days_ago' => 45,
            ],
            [
                'name' => 'Pedro Almeida',
                'email' => 'pedro.almeida@email.com',
                'phone' => '11987654326',
                'cpf' => '67890123456',
                'plan' => $plans->where('billing_cycle', 'monthly')->first(),
                'started_days_ago' => 20,
            ],
            [
                'name' => 'Fernanda Lima',
                'email' => 'fernanda.lima@email.com',
                'phone' => '11987654327',
                'cpf' => '78901234567',
                'plan' => $plans->where('billing_cycle', 'annual')->first(),
                'started_days_ago' => 90,
            ],
            [
                'name' => 'Ricardo Souza',
                'email' => 'ricardo.souza@email.com',
                'phone' => '11987654328',
                'cpf' => '89012345678',
                'plan' => $plans->where('billing_cycle', 'semiannual')->first(),
                'started_days_ago' => 75,
            ],
            [
                'name' => 'Camila Ferreira',
                'email' => 'camila.ferreira@email.com',
                'phone' => '11987654329',
                'cpf' => '90123456789',
                'plan' => $plans->where('billing_cycle', 'monthly')->first(),
                'started_days_ago' => 10,
            ],
            [
                'name' => 'Bruno Martins',
                'email' => 'bruno.martins@email.com',
                'phone' => '11987654330',
                'cpf' => '01234567890',
                'plan' => $plans->where('billing_cycle', 'quarterly')->first(),
                'started_days_ago' => 25,
            ],
        ];

        $this->command->info('Criando alunos com planos ativos...');
        
        foreach ($students as $studentData) {
            $plan = $studentData['plan'];
            
            if (!$plan) {
                $this->command->warn("Plano não encontrado para {$studentData['name']}");
                continue;
            }

            // Verificar se usuário já existe
            $existingUser = User::where('email', $studentData['email'])->first();
            if ($existingUser) {
                $this->command->warn("✗ {$studentData['name']} já existe, pulando...");
                continue;
            }

            // Criar usuário
            $user = User::create([
                'name' => $studentData['name'],
                'email' => $studentData['email'],
                'password' => bcrypt('senha123'),
                'phone' => $studentData['phone'],
                'document' => $studentData['cpf'],
                'role' => 'student',
            ]);

            // Criar plano do usuário
            $startsAt = now()->subDays($studentData['started_days_ago']);
            $endsAt = $startsAt->copy()->addDays($plan->duration_days);
            
            $userPlan = UserPlan::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => 'active',
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
            ]);

            // Criar pagamento confirmado
            Payment::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'amount' => $plan->price,
                'status' => 'paid',
                'payment_method' => rand(0, 1) ? 'pix' : 'boleto',
                'asaas_payment_id' => 'pay_demo_' . uniqid(),
                'due_date' => $startsAt,
                'paid_at' => $startsAt,
            ]);

            $this->command->info("✓ {$user->name} - {$plan->name} (ativo até {$endsAt->format('d/m/Y')})");
        }

        // Criar algumas reservas para os alunos
        $this->command->info("\nCriando reservas...");
        
        $schedules = Schedule::where('status', 'available')->get();
        $createdStudents = User::where('role', 'student')->get();
        
        if ($schedules->isNotEmpty()) {
            foreach ($createdStudents->take(5) as $student) {
                // 2-5 reservas confirmadas no passado
                for ($i = 0; $i < rand(2, 5); $i++) {
                    $schedule = $schedules->random();
                    $bookingDate = now()->subDays(rand(1, 15));
                    
                    Booking::create([
                        'user_id' => $student->id,
                        'schedule_id' => $schedule->id,
                        'date' => $bookingDate,
                        'status' => rand(0, 10) > 2 ? 'attended' : 'confirmed',
                    ]);
                }

                // 1-2 reservas futuras
                for ($i = 0; $i < rand(1, 2); $i++) {
                    $schedule = $schedules->random();
                    $bookingDate = now()->addDays(rand(1, 7));
                    
                    Booking::create([
                        'user_id' => $student->id,
                        'schedule_id' => $schedule->id,
                        'date' => $bookingDate,
                        'status' => 'confirmed',
                    ]);
                }
            }
            
            $this->command->info("✓ Reservas criadas com sucesso");
        } else {
            $this->command->warn("Nenhum horário disponível encontrado. Pule a criação de reservas.");
        }

        // Criar alguns pagamentos pendentes
        $this->command->info("\nCriando pagamentos pendentes...");
        
        $studentsForPending = $createdStudents->random(min(3, $createdStudents->count()));

        foreach ($studentsForPending as $student) {
            $plan = $plans->random();
            
            Payment::create([
                'user_id' => $student->id,
                'plan_id' => $plan->id,
                'amount' => $plan->price,
                'status' => 'pending',
                'payment_method' => 'pix',
                'asaas_payment_id' => 'pay_pending_' . uniqid(),
                'due_date' => now()->addDays(3),
            ]);
        }

        $this->command->info("✓ Pagamentos pendentes criados");

        // Resumo
        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('📊 RESUMO DOS DADOS CRIADOS');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->newLine();
        
        $this->command->table(
            ['Aluno', 'Email', 'Senha', 'Plano', 'Válido até'],
            collect($students)->map(function ($s) {
                $user = User::where('email', $s['email'])->first();
                if (!$user) return null;
                
                $userPlan = UserPlan::where('user_id', $user->id)
                    ->where('status', 'active')
                    ->first();
                    
                return [
                    $s['name'],
                    $s['email'],
                    'senha123',
                    $s['plan']->name ?? 'N/A',
                    $userPlan && $userPlan->ends_at ? 
                        (is_string($userPlan->ends_at) ? $userPlan->ends_at : $userPlan->ends_at->format('d/m/Y')) : 
                        'N/A',
                ];
            })->filter()
        );

        $this->command->newLine();
        $this->command->info('💰 ESTATÍSTICAS:');
        $this->command->info("   • Total de alunos: " . User::where('role', 'student')->count());
        $this->command->info("   • Planos ativos: " . UserPlan::where('status', 'active')->count());
        $this->command->info("   • Pagamentos confirmados: R$ " . number_format(Payment::where('status', 'paid')->sum('amount'), 2, ',', '.'));
        $this->command->info("   • Pagamentos pendentes: R$ " . number_format(Payment::where('status', 'pending')->sum('amount'), 2, ',', '.'));
        $this->command->info("   • Total de reservas: " . Booking::count());
        $this->command->info("   • Reservas confirmadas: " . Booking::where('status', 'confirmed')->count());
        $this->command->info("   • Aulas realizadas: " . Booking::where('status', 'attended')->count());
        
        $this->command->newLine();
        $this->command->info('✅ Dados de demonstração criados com sucesso!');
        $this->command->newLine();
    }
}
