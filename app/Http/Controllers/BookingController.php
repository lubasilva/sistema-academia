<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Setting;
use App\Services\ScheduleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;

class BookingController extends Controller
{
    use AuthorizesRequests;
    
    protected $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Booking::class);
        return view('bookings.index');
    }

    /**
     * Get bookings for calendar (API)
     */
    public function getBookings(Request $request)
    {
        $query = Booking::with(['user', 'schedule']);

        if (Auth::user()->role === 'student') {
            $query->where('user_id', Auth::id());
        }

        $bookings = $query->get()->map(function ($booking) {
            $colors = [
                'booked' => '#28a745',
                'attended' => '#0dcaf0',
                'canceled' => '#dc3545',
                'absent' => '#6c757d',
            ];

            // Usar o campo 'time' se disponível, caso contrário usar o schedule
            $time = $booking->time 
                ? $booking->time 
                : ($booking->schedule ? Carbon::parse($booking->schedule->starts_at)->format('H:i:s') : '00:00:00');

            return [
                'id' => $booking->id,
                'title' => $booking->user->name,
                'start' => $booking->date->format('Y-m-d') . ' ' . $time,
                'backgroundColor' => $colors[$booking->status] ?? '#6c757d',
                'borderColor' => $colors[$booking->status] ?? '#6c757d',
            ];
        });

        return response()->json($bookings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Booking::class);

        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $userId = $request->user_id ?? Auth::id();
        $user = \App\Models\User::with('activePlan.plan')->findOrFail($userId);

        // Verificar se o horário está disponível
        $dateTime = Carbon::parse($validated['date'] . ' ' . $validated['time']);
        if (!$this->scheduleService->isSlotAvailable($dateTime)) {
            return back()->with('error', 'Horário não disponível (bloqueado ou fora do horário de funcionamento).');
        }

        // Verificar plano ativo
        if (!$user->activePlan) {
            return back()->with('error', 'Usuário não possui plano ativo.');
        }

        // Verificar créditos (regulares + extras)
        $totalCredits = $user->activePlan->total_credits;
        if ($totalCredits <= 0) {
            return back()->with('error', 'Créditos insuficientes.');
        }

        // Verificar limite de reservas por semana (apenas para não-admin e apenas com créditos regulares)
        // Com créditos extras, pode marcar quantas vezes quiser (se houver vaga)
        if (Auth::user()->role !== 'admin' && $user->activePlan->credits_remaining > 0) {
            $frequencyPerWeek = $user->activePlan->plan->frequency_per_week;
            
            // Calcular início e fim da semana (segunda a sexta)
            $requestedDate = Carbon::parse($validated['date']);
            $startOfWeek = $requestedDate->copy()->startOfWeek(Carbon::MONDAY);
            $endOfWeek = $startOfWeek->copy()->addDays(4);
            
            // Contar reservas ativas na semana (segunda a sexta)
            $bookingsThisWeek = Booking::where('user_id', $userId)
                ->whereBetween('date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
                ->whereIn('status', ['booked', 'attended'])
                ->count();
            
            if ($bookingsThisWeek >= $frequencyPerWeek) {
                // Se tiver créditos extras, pode usar para repor
                if ($user->activePlan->extra_credits > 0) {
                    // Permitir com crédito extra
                } else {
                    return back()->with('error', "Você já atingiu o limite de {$frequencyPerWeek}x por semana do seu plano.");
                }
            }
        }

        // Verificar capacidade
        $defaultCapacity = (int) Setting::get('max_capacity_per_class', 20);
        $bookedCount = Booking::where('date', $validated['date'])
            ->where('time', $validated['time'])
            ->where('status', '!=', 'cancelled')
            ->count();

        if ($bookedCount >= $defaultCapacity) {
            return back()->with('error', 'Turma lotada.');
        }

        // Verificar se a aula já passou
        if ($dateTime->isPast()) {
            return back()->with('error', 'Não é possível reservar aula no passado.');
        }
        
        // Verificar antecedência mínima (apenas para não-admin)
        if (Auth::user()->role !== 'admin') {
            $minHours = (int) Setting::get('min_booking_hours', 2);
            $hoursUntilClass = now()->diffInHours($dateTime, false);
            if ($hoursUntilClass < $minHours) {
                return back()->with('error', "Reserva deve ser feita com pelo menos {$minHours}h de antecedência.");
            }
        }

        DB::beginTransaction();
        try {
            // Criar reserva
            $booking = Booking::create([
                'user_id' => $userId,
                'date' => $validated['date'],
                'time' => $validated['time'],
                'status' => 'booked',
                'created_by' => Auth::id(),
            ]);

            // Usar crédito (prioriza regular, depois extra) com log automático
            $user->activePlan->useCredit(Auth::id(), $booking->id);

            DB::commit();

            $creditType = $user->activePlan->credits_remaining > 0 ? 'regular' : 'extra (reposição)';
            
            // Passar ID da reserva para permitir criar recorrência
            return redirect()->route('bookings.index')
                ->with('success', "Reserva criada com sucesso! Crédito {$creditType} utilizado.")
                ->with('booking_id', $booking->id)
                ->with('show_recurring_modal', true);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao criar reserva: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        $this->authorize('delete', $booking);

        DB::beginTransaction();
        try {
            // Verificar prazo de cancelamento
            $minHours = (int) Setting::get('min_cancel_hours', 2);
            
            // Usar o campo 'time' se disponível, caso contrário usar o schedule
            $time = $booking->time 
                ? $booking->time 
                : ($booking->schedule ? Carbon::parse($booking->schedule->starts_at)->format('H:i:s') : '00:00:00');
                
            $bookingDateTime = Carbon::parse($booking->date->format('Y-m-d') . ' ' . $time);
            
            if ($bookingDateTime->diffInHours(now(), false) < $minHours) {
                return back()->with('error', "Cancelamento deve ser feito com {$minHours}h de antecedência.");
            }

            $booking->update(['status' => 'cancelled']);

            // Devolver crédito como EXTRA (para reposição) com log automático
            if ($booking->user->activePlan) {
                $booking->user->activePlan->returnCredit(
                    Auth::id(), 
                    'Cancelamento de aula - crédito devolvido para reposição',
                    $booking->id
                );
            }

            DB::commit();

            return redirect()->route('bookings.index')
                ->with('success', 'Reserva cancelada! Crédito devolvido como crédito extra para reposição.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao cancelar reserva: ' . $e->getMessage());
        }
    }
    
    /**
     * Transforma uma reserva em recorrente
     */
    public function makeRecurring(Booking $booking)
    {
        $this->authorize('update', $booking);
        
        $recurringService = app(\App\Services\RecurringBookingService::class);
        
        // Obter dia da semana e horário da reserva
        $date = Carbon::parse($booking->date);
        $dayOfWeek = strtolower($date->format('l')); // monday, tuesday, etc
        $time = $booking->time;
        
        // Verificar se já existe padrão recorrente para este usuário/dia/horário
        $existing = \App\Models\RecurringBooking::where('user_id', $booking->user_id)
            ->where('day_of_week', $dayOfWeek)
            ->where('time', $time)
            ->where('is_active', true)
            ->first();
        
        if ($existing) {
            return back()->with('error', 'Você já possui uma reserva recorrente para este dia e horário.');
        }
        
        DB::beginTransaction();
        try {
            // Associar reserva atual ao novo padrão recorrente
            $recurring = $recurringService->createRecurringPattern($booking->user, $dayOfWeek, $time);
            $booking->update(['recurring_booking_id' => $recurring->id]);
            $recurring->update(['last_created_date' => $booking->date]);
            
            // Gerar reservas futuras
            $createdCount = $recurringService->generateFutureBookings($recurring);
            
            DB::commit();
            
            if ($createdCount > 0) {
                return redirect()->route('bookings.index')
                    ->with('success', "Reserva recorrente criada! {$createdCount} aula(s) foram agendadas automaticamente.");
            } else {
                return redirect()->route('bookings.index')
                    ->with('info', 'Reserva recorrente criada, mas não foi possível agendar aulas futuras (créditos insuficientes ou sem vagas).');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao criar reserva recorrente: ' . $e->getMessage());
        }
    }
    
    /**
     * Cancela todas as reservas futuras de um padrão recorrente
     */
    public function cancelRecurring(Booking $booking)
    {
        $this->authorize('delete', $booking);
        
        if (!$booking->isRecurring()) {
            return back()->with('error', 'Esta reserva não faz parte de uma recorrência.');
        }
        
        $recurringService = app(\App\Services\RecurringBookingService::class);
        
        DB::beginTransaction();
        try {
            $fromDate = Carbon::parse($booking->date)->addWeek(); // A partir da próxima semana
            $cancelledCount = $recurringService->cancelFutureBookings($booking->recurringBooking, $fromDate);
            
            // Desativar o padrão recorrente
            $booking->recurringBooking->deactivate();
            
            DB::commit();
            
            return redirect()->route('bookings.index')
                ->with('success', "Recorrência cancelada! {$cancelledCount} aula(s) futuras foram canceladas e os créditos devolvidos.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao cancelar recorrência: ' . $e->getMessage());
        }
    }
}
