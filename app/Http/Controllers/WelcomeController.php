<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Schedule;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        // Buscar planos ativos
        $plans = Plan::where('is_active', true)
            ->orderBy('price_cents')
            ->get();

        // Buscar próximos horários disponíveis (próximos 7 dias)
        $startDate = Carbon::now();
        $endDate = Carbon::now()->addDays(7);
        
        $schedules = Schedule::whereBetween('starts_at', [$startDate, $endDate])
            ->where('status', 'open')
            ->orderBy('starts_at')
            ->take(10)
            ->get()
            ->map(function ($schedule) {
                $bookingsCount = Booking::where('schedule_id', $schedule->id)
                    ->whereIn('status', ['confirmed', 'attended'])
                    ->count();
                
                $maxCapacity = $schedule->capacity_override ?? 10; // Capacidade padrão
                $schedule->available_spots = $maxCapacity - $bookingsCount;
                $schedule->is_full = $bookingsCount >= $maxCapacity;
                return $schedule;
            });

        // Estatísticas para mostrar
        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'total_classes' => Schedule::where('starts_at', '>=', Carbon::now())->count(),
            'total_instructors' => User::where('role', 'instructor')->count(),
            'satisfaction_rate' => 98, // Pode ser calculado com um sistema de avaliações
        ];

        // Depoimentos fictícios
        $testimonials = [
            [
                'name' => 'Marina Silva',
                'role' => 'Praticante há 2 anos',
                'image' => 'https://randomuser.me/api/portraits/women/44.jpg',
                'text' => 'O StudioFit mudou minha vida! A atenção personalizada do instrutor faz toda a diferença. Perdi 15kg e ganhei muita qualidade de vida.',
                'rating' => 5,
            ],
            [
                'name' => 'Carlos Eduardo',
                'role' => 'Praticante há 1 ano',
                'image' => 'https://randomuser.me/api/portraits/men/32.jpg',
                'text' => 'Ambiente acolhedor, equipamentos modernos e instrutores altamente capacitados. Nunca me senti tão motivado a treinar!',
                'rating' => 5,
            ],
            [
                'name' => 'Ana Paula',
                'role' => 'Praticante há 6 meses',
                'image' => 'https://randomuser.me/api/portraits/women/65.jpg',
                'text' => 'A flexibilidade de horários e o sistema de agendamento online facilitam muito. Consigo encaixar os treinos na minha rotina sem estresse.',
                'rating' => 5,
            ],
        ];

        // Benefícios do studio
        $benefits = [
            [
                'icon' => 'person-check',
                'title' => 'Atenção Personalizada',
                'description' => 'Cada aluno recebe um plano de treino exclusivo, adaptado aos seus objetivos e necessidades.',
            ],
            [
                'icon' => 'people',
                'title' => 'Turmas Reduzidas',
                'description' => 'Máximo de 10 alunos por aula, garantindo qualidade no atendimento e correção de movimentos.',
            ],
            [
                'icon' => 'clock-history',
                'title' => 'Horários Flexíveis',
                'description' => 'Agende suas aulas quando quiser através do nosso sistema online. Das 6h às 22h.',
            ],
            [
                'icon' => 'trophy',
                'title' => 'Instrutores Certificados',
                'description' => 'Profissionais graduados e com especializações, focados em resultados e segurança.',
            ],
            [
                'icon' => 'house-heart',
                'title' => 'Ambiente Exclusivo',
                'description' => 'Espaço clean e climatizado, sem lotação. Você treina com tranquilidade e foco.',
            ],
            [
                'icon' => 'graph-up-arrow',
                'title' => 'Acompanhamento de Resultados',
                'description' => 'Avaliações periódicas e ajustes constantes para você alcançar suas metas mais rápido.',
            ],
        ];

        return view('welcome', compact('plans', 'schedules', 'stats', 'testimonials', 'benefits'));
    }
}
