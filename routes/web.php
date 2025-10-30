<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Agenda e Reservas
    Route::resource('bookings', BookingController::class);
    
    // Pagamentos
    Route::resource('payments', \App\Http\Controllers\PaymentController::class)->only(['index', 'create', 'store', 'show']);
    
    // Admin routes
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class);
        Route::resource('plans', \App\Http\Controllers\PlanController::class);
        Route::get('admin/payments', [\App\Http\Controllers\PaymentController::class, 'adminIndex'])->name('admin.payments.index');
        Route::resource('schedules', \App\Http\Controllers\ScheduleController::class);
        Route::get('settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
        Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    });
    
    // API routes for calendar
    Route::get('/api/bookings', [BookingController::class, 'getBookings'])->name('api.bookings');
    Route::get('/api/schedules/available', function(Illuminate\Http\Request $request) {
        $date = $request->date;
        
        // Buscar horários do dia (starts_at no mesmo dia)
        $schedules = \App\Models\Schedule::whereDate('starts_at', $date)
            ->where('status', 'open')
            ->get()
            ->map(function($schedule) use ($date) {
                $maxCapacity = \App\Models\Setting::where('key', 'max_capacity_per_class')->value('value') ?? 10;
                $booked = \App\Models\Booking::where('date', $date)
                    ->where('schedule_id', $schedule->id)
                    ->where('status', '!=', 'cancelled')
                    ->count();
                
                return [
                    'id' => $schedule->id,
                    'time' => \Carbon\Carbon::parse($schedule->starts_at)->format('H:i'),
                    'available_slots' => max(0, $maxCapacity - $booked),
                ];
            });
        
        return response()->json($schedules);
    })->name('api.schedules.available');
    
    Route::get('/api/schedules/occupation', function(Illuminate\Http\Request $request) {
        $start = $request->start;
        $end = $request->end;
        
        // Buscar todos os horários no período
        $schedules = \App\Models\Schedule::whereBetween('starts_at', [$start, $end])
            ->where('status', 'open')
            ->get();
        
        $maxCapacity = \App\Models\Setting::where('key', 'max_capacity_per_class')->value('value') ?? 10;
        
        $occupationEvents = [];
        
        foreach ($schedules as $schedule) {
            // Para cada horário, precisamos verificar cada dia no intervalo
            $scheduleDate = \Carbon\Carbon::parse($schedule->starts_at);
            $currentDate = \Carbon\Carbon::parse($start)->startOfDay();
            $endDate = \Carbon\Carbon::parse($end)->startOfDay();
            
            while ($currentDate->lte($endDate)) {
                // Contar reservas nesse horário nesse dia
                $count = \App\Models\Booking::where('schedule_id', $schedule->id)
                    ->where('date', $currentDate->format('Y-m-d'))
                    ->whereIn('status', ['booked', 'attended'])
                    ->count();
                
                if ($count > 0) {
                    // Combinar a data com o horário do schedule
                    $eventStart = $currentDate->copy()
                        ->setTimeFrom($scheduleDate);
                    
                    $eventEnd = $eventStart->copy()->addHour();
                    
                    $occupationEvents[] = [
                        'start' => $eventStart->toIso8601String(),
                        'end' => $eventEnd->toIso8601String(),
                        'display' => 'background',
                        'classNames' => ['occupation-bg'],
                        'extendedProps' => [
                            'isOccupation' => true,
                            'count' => $count,
                            'capacity' => $maxCapacity,
                        ],
                    ];
                }
                
                $currentDate->addDay();
            }
        }
        
        return response()->json($occupationEvents);
    })->name('api.schedules.occupation');

});

require __DIR__.'/auth.php';

