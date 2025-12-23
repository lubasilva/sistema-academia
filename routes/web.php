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
    Route::post('bookings/{booking}/make-recurring', [BookingController::class, 'makeRecurring'])->name('bookings.make-recurring');
    Route::post('bookings/{booking}/cancel-recurring', [BookingController::class, 'cancelRecurring'])->name('bookings.cancel-recurring');
    
    // Treinos
    Route::resource('workouts', \App\Http\Controllers\WorkoutController::class);
    Route::post('workouts/simple', [\App\Http\Controllers\WorkoutController::class, 'storeSimple'])->name('workouts.store.simple');
    
    // Pagamentos
    Route::resource('payments', \App\Http\Controllers\PaymentController::class)->only(['index', 'create', 'store', 'show']);
    
    // Admin routes
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class);
        Route::resource('plans', \App\Http\Controllers\PlanController::class);
        Route::get('admin/payments', [\App\Http\Controllers\PaymentController::class, 'adminIndex'])->name('admin.payments.index');
        Route::resource('schedules', \App\Http\Controllers\ScheduleController::class);
        
        // Gerenciamento de bloqueios de horários
        Route::resource('admin/schedule-blocks', \App\Http\Controllers\Admin\ScheduleBlockController::class, [
            'as' => 'admin'
        ]);
        
        // Gerenciamento de alunos
        Route::get('admin/students', [\App\Http\Controllers\Admin\StudentManagementController::class, 'index'])->name('admin.students.index');
        Route::get('admin/students/{student}', [\App\Http\Controllers\Admin\StudentManagementController::class, 'show'])->name('admin.students.show');
        Route::post('admin/students/{student}/extra-credits', [\App\Http\Controllers\Admin\StudentManagementController::class, 'addExtraCredits'])->name('admin.students.add-extra-credits');
        Route::post('admin/students/{student}/observation', [\App\Http\Controllers\Admin\StudentManagementController::class, 'addObservation'])->name('admin.students.add-observation');
        Route::post('admin/students/{student}/bioimpedance', [\App\Http\Controllers\Admin\StudentManagementController::class, 'storeBioimpedance'])->name('admin.students.bioimpedance.store');
        Route::post('admin/bookings/{booking}/attendance', [\App\Http\Controllers\Admin\StudentManagementController::class, 'markAttendance'])->name('admin.bookings.mark-attendance');
        
        Route::get('settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
        Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    });
    
    // API routes for calendar
    Route::get('/api/bookings', [BookingController::class, 'getBookings'])->name('api.bookings');
    
    // API para buscar alunos com créditos disponíveis
    Route::get('/api/students/with-credits', function(Illuminate\Http\Request $request) {
        try {
            $search = $request->get('search', '');
            
            $students = \App\Models\User::where('role', 'student')
                ->with(['activePlan.plan'])
                ->when($search, function($query, $search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                })
                ->get()
                ->filter(function($student) {
                    // Filtrar apenas alunos com plano ativo e créditos disponíveis
                    if (!$student->activePlan) return false;
                    
                    $plan = $student->activePlan->plan;
                    if (!$plan) return false;
                    
                    $usedCredits = $student->activePlan->total_credits_used ?? 0;
                    $extraCredits = $student->activePlan->extra_credits ?? 0;
                    
                    if ($plan->type === 'unlimited') {
                        // Plano ilimitado sempre tem créditos
                        return $student->activePlan->ends_at >= now();
                    } else {
                        // Plano limitado: verificar créditos normais + extras
                        $totalAvailable = ($plan->credits - $usedCredits) + $extraCredits;
                        return $totalAvailable > 0 && $student->activePlan->ends_at >= now();
                    }
                })
                ->map(function($student) {
                    $plan = $student->activePlan->plan;
                    $usedCredits = $student->activePlan->total_credits_used ?? 0;
                    $extraCredits = $student->activePlan->extra_credits ?? 0;
                    
                    if ($plan->type === 'unlimited') {
                        $creditsInfo = 'Ilimitado';
                    } else {
                        $remaining = $plan->credits - $usedCredits;
                        $total = $remaining + $extraCredits;
                        $creditsInfo = "{$total} créditos";
                        if ($extraCredits > 0) {
                            $creditsInfo .= " ({$remaining} + {$extraCredits} extras)";
                        }
                    }
                    
                    return [
                        'id' => $student->id,
                        'name' => $student->name,
                        'email' => $student->email,
                        'plan_name' => $plan->name,
                        'credits_info' => $creditsInfo,
                    ];
                })
                ->values();
            
            return response()->json($students);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erro ao buscar alunos com créditos: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    })->name('api.students.with-credits');
    
    Route::get('/api/schedules/available', function(Illuminate\Http\Request $request) {
        $date = $request->date;
        
        // Usar o novo ScheduleService para buscar horários disponíveis
        $scheduleService = app(\App\Services\ScheduleService::class);
        $slots = $scheduleService->getAvailableSlots($date);
        
        $defaultCapacity = (int) \App\Models\Setting::get('max_capacity_per_class', 20);
        
        $schedules = collect($slots)->map(function($slot) use ($date, $defaultCapacity) {
            $startTime = $slot['starts_at']->format('H:i:s');
            
            // Contar reservas existentes para este horário (excluindo cancelados)
            $booked = \App\Models\Booking::where('date', $date)
                ->whereTime('time', $startTime)
                ->whereIn('status', ['booked', 'attended'])
                ->count();
            
            return [
                'time' => $slot['starts_at']->format('H:i'),
                'available_slots' => max(0, $slot['capacity'] - $booked),
                'capacity' => $slot['capacity'],
            ];
        });
        
        return response()->json($schedules);
    })->name('api.schedules.available');
    
    Route::get('/api/schedules/occupation', function(Illuminate\Http\Request $request) {
        $start = $request->start;
        $end = $request->end;
        
        $scheduleService = app(\App\Services\ScheduleService::class);
        $defaultCapacity = (int) \App\Models\Setting::get('max_capacity_per_class', 20);
        $slotDuration = (int) \App\Models\Setting::get('slot_duration', 60);
        $occupationEvents = [];
        
        // Percorrer cada dia no intervalo
        $currentDate = \Carbon\Carbon::parse($start)->startOfDay();
        $endDate = \Carbon\Carbon::parse($end)->startOfDay();
        
        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            
            // Buscar TODOS os bookings do dia e agrupar por horário
            $bookingsByTime = \App\Models\Booking::where('date', $dateStr)
                ->whereIn('status', ['booked', 'attended'])
                ->with('user:id,name')
                ->get()
                ->groupBy('time');
            
            // Para cada horário com bookings, criar evento de ocupação
            foreach ($bookingsByTime as $time => $bookings) {
                $count = $bookings->count();
                
                // Criar datetime completo para o início e fim (com timezone explícito)
                $startDateTime = \Carbon\Carbon::parse($dateStr . ' ' . $time, 'America/Sao_Paulo');
                $endDateTime = $startDateTime->copy()->addMinutes($slotDuration);
                
                // Extrair nomes dos alunos
                $students = $bookings->map(function($booking) {
                    return $booking->user ? $booking->user->name : 'Usuário desconhecido';
                })->toArray();
                
                $occupationEvents[] = [
                    'start' => $startDateTime->toIso8601String(),
                    'end' => $endDateTime->toIso8601String(),
                    'display' => 'background',
                    'classNames' => ['occupation-bg'],
                    'extendedProps' => [
                        'isOccupation' => true,
                        'count' => $count,
                        'capacity' => $defaultCapacity,
                        'students' => $students,
                    ],
                ];
            }
            
            $currentDate->addDay();
        }
        
        return response()->json($occupationEvents);
    })->name('api.schedules.occupation');

});

require __DIR__.'/auth.php';

