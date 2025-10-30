<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BookingController extends Controller
{
    use AuthorizesRequests;
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

            return [
                'id' => $booking->id,
                'title' => $booking->user->name,
                'start' => $booking->date . ' ' . \Carbon\Carbon::parse($booking->schedule->starts_at)->format('H:i:s'),
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
            'schedule_id' => 'required|exists:schedules,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $userId = $request->user_id ?? Auth::id();
        $user = \App\Models\User::with('activePlan.plan')->findOrFail($userId);

        // Verificar plano ativo
        if (!$user->activePlan) {
            return back()->with('error', 'Usuário não possui plano ativo.');
        }

        // Verificar créditos
        if ($user->activePlan->credits_remaining <= 0) {
            return back()->with('error', 'Créditos insuficientes.');
        }

        // Verificar limite de reservas por semana (apenas para não-admin)
        if (Auth::user()->role !== 'admin') {
            $frequencyPerWeek = $user->activePlan->plan->frequency_per_week;
            
            // Calcular início e fim da semana (segunda a sexta)
            $requestedDate = \Carbon\Carbon::parse($validated['date']);
            $startOfWeek = $requestedDate->copy()->startOfWeek(\Carbon\Carbon::MONDAY); // Segunda-feira
            $endOfWeek = $startOfWeek->copy()->addDays(4); // Sexta-feira (segunda + 4 dias)
            
            // Contar reservas ativas na semana (segunda a sexta)
            $bookingsThisWeek = Booking::where('user_id', $userId)
                ->whereBetween('date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
                ->whereIn('status', ['booked', 'attended'])
                ->count();
            
            if ($bookingsThisWeek >= $frequencyPerWeek) {
                return back()->with('error', "Você já atingiu o limite de {$frequencyPerWeek}x por semana do seu plano.");
            }
        }

        // Verificar capacidade
        $maxCapacity = Setting::where('key', 'max_capacity_per_class')->value('value') ?? 10;
        $bookedCount = Booking::where('date', $validated['date'])
            ->where('schedule_id', $validated['schedule_id'])
            ->where('status', '!=', 'cancelled')
            ->count();

        if ($bookedCount >= $maxCapacity) {
            return back()->with('error', 'Turma lotada.');
        }

        // Verificar antecedência mínima
        $minHours = Setting::where('key', 'min_booking_hours')->value('value') ?? 2;
        $schedule = Schedule::findOrFail($validated['schedule_id']);
        $bookingDateTime = \Carbon\Carbon::parse($validated['date'] . ' ' . \Carbon\Carbon::parse($schedule->starts_at)->format('H:i:s'));
        
        // Verificar se a aula já passou
        if ($bookingDateTime->isPast()) {
            return back()->with('error', 'Não é possível reservar aula no passado.');
        }
        
        // Verificar antecedência mínima (apenas para não-admin)
        if (Auth::user()->role !== 'admin') {
            $hoursUntilClass = now()->diffInHours($bookingDateTime, false);
            if ($hoursUntilClass < $minHours) {
                return back()->with('error', "Reserva deve ser feita com pelo menos {$minHours}h de antecedência.");
            }
        }

        DB::beginTransaction();
        try {
            // Criar reserva
            $booking = Booking::create([
                'user_id' => $userId,
                'schedule_id' => $validated['schedule_id'],
                'date' => $validated['date'],
                'status' => 'booked',
                'created_by' => Auth::id(),
            ]);

            // Deduzir crédito
            $user->activePlan->decrement('credits_remaining');

            DB::commit();

            return redirect()->route('bookings.index')->with('success', 'Reserva criada com sucesso!');
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
            $minHours = Setting::where('key', 'min_cancel_hours')->value('value') ?? 2;
            $bookingDateTime = \Carbon\Carbon::parse($booking->date . ' ' . \Carbon\Carbon::parse($booking->schedule->starts_at)->format('H:i:s'));
            
            if ($bookingDateTime->diffInHours(now(), false) < $minHours) {
                return back()->with('error', "Cancelamento deve ser feito com {$minHours}h de antecedência.");
            }

            $booking->update(['status' => 'cancelled']);

            // Devolver crédito
            if ($booking->user->activePlan) {
                $booking->user->activePlan->increment('credits_remaining');
            }

            DB::commit();

            return redirect()->route('bookings.index')->with('success', 'Reserva cancelada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao cancelar reserva: ' . $e->getMessage());
        }
    }
}

