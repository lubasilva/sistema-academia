<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\RecurringBooking;
use App\Models\User;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RecurringBookingService
{
    /**
     * Cria um padrão de reserva recorrente e gera as reservas futuras
     */
    public function createRecurringPattern(User $user, string $dayOfWeek, string $time): RecurringBooking
    {
        $recurring = RecurringBooking::create([
            'user_id' => $user->id,
            'day_of_week' => $dayOfWeek,
            'time' => $time,
            'is_active' => true,
        ]);
        
        // Criar reservas futuras
        $this->generateFutureBookings($recurring);
        
        return $recurring;
    }
    
    /**
     * Gera todas as reservas futuras para um padrão recorrente
     */
    public function generateFutureBookings(RecurringBooking $recurring): int
    {
        $user = $recurring->user;
        $activePlan = $user->activePlan;
        
        if (!$activePlan) {
            return 0;
        }
        
        $plan = $activePlan->plan;
        $maxCapacity = (int) Setting::get('max_capacity_per_class', 20);
        
        // Calcular quantas reservas ainda podem ser feitas
        $usedCredits = $activePlan->total_credits_used ?? 0;
        $extraCredits = $activePlan->extra_credits ?? 0;
        
        if ($plan->type === 'unlimited') {
            // Para planos ilimitados, criar até a data de expiração
            $availableCredits = 1000; // Número grande para não limitar
        } else {
            $remaining = $plan->credits - $usedCredits;
            $availableCredits = $remaining + $extraCredits;
        }
        
        if ($availableCredits <= 0) {
            return 0;
        }
        
        // Começar da próxima ocorrência do dia da semana
        $startDate = $recurring->last_created_date 
            ? Carbon::parse($recurring->last_created_date)->addWeek() 
            : Carbon::now()->next($recurring->day_of_week);
        
        $endDate = Carbon::parse($activePlan->ends_at);
        $createdCount = 0;
        $creditsUsed = 0;
        
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate) && $creditsUsed < $availableCredits) {
            $dateStr = $currentDate->format('Y-m-d');
            
            // Verificar se horário está bloqueado
            if (\App\Models\ScheduleBlock::isBlocked($dateStr, $recurring->time)) {
                $currentDate->addWeek();
                continue;
            }
            
            // Verificar se já existe reserva neste dia/horário para este usuário
            $existingBooking = Booking::where('user_id', $user->id)
                ->where('date', $dateStr)
                ->where('time', $recurring->time)
                ->where('status', '!=', 'cancelled')
                ->first();
            
            if ($existingBooking) {
                $currentDate->addWeek();
                continue;
            }
            
            // Verificar capacidade
            $bookedCount = Booking::where('date', $dateStr)
                ->where('time', $recurring->time)
                ->whereIn('status', ['booked', 'attended'])
                ->count();
            
            if ($bookedCount >= $maxCapacity) {
                $currentDate->addWeek();
                continue;
            }
            
            // Criar a reserva
            Booking::create([
                'user_id' => $user->id,
                'date' => $dateStr,
                'time' => $recurring->time,
                'status' => 'booked',
                'created_by' => $user->id,
                'recurring_booking_id' => $recurring->id,
            ]);
            
            $createdCount++;
            $creditsUsed++;
            $recurring->update(['last_created_date' => $dateStr]);
            
            $currentDate->addWeek();
        }
        
        return $createdCount;
    }
    
    /**
     * Cancela todas as reservas futuras de um padrão recorrente
     */
    public function cancelFutureBookings(RecurringBooking $recurring, ?Carbon $fromDate = null): int
    {
        $query = Booking::where('recurring_booking_id', $recurring->id)
            ->where('status', 'booked')
            ->where('date', '>=', now()->format('Y-m-d'));
        
        if ($fromDate) {
            $query->where('date', '>=', $fromDate->format('Y-m-d'));
        }
        
        $count = $query->count();
        $query->update(['status' => 'cancelled']);
        
        return $count;
    }
    
    /**
     * Move todas as reservas futuras para um novo horário
     */
    public function changeFutureBookings(RecurringBooking $recurring, string $newTime, ?Carbon $fromDate = null): int
    {
        $fromDateStr = $fromDate ? $fromDate->format('Y-m-d') : now()->format('Y-m-d');
        
        $futureBookings = Booking::where('recurring_booking_id', $recurring->id)
            ->where('status', 'booked')
            ->where('date', '>=', $fromDateStr)
            ->get();
        
        $changedCount = 0;
        
        foreach ($futureBookings as $booking) {
            // Verificar se novo horário tem capacidade
            $maxCapacity = (int) Setting::get('max_capacity_per_class', 20);
            $bookedCount = Booking::where('date', $booking->date)
                ->where('time', $newTime)
                ->whereIn('status', ['booked', 'attended'])
                ->count();
            
            if ($bookedCount < $maxCapacity) {
                $booking->update(['time' => $newTime]);
                $changedCount++;
            }
        }
        
        // Atualizar o padrão recorrente
        $recurring->update(['time' => $newTime]);
        
        return $changedCount;
    }
}
