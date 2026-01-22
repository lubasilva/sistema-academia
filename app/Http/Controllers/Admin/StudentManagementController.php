<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use App\Models\CreditLog;
use App\Models\BioimpedanceMeasurement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentManagementController extends Controller
{
    /**
     * Lista todos os alunos com estatísticas
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status', 'all');

        $students = User::where('role', 'aluno')
            ->with(['activePlan.plan', 'activePlan.creditLogs'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($status !== 'all', function ($query) use ($status) {
                if ($status === 'active') {
                    $query->whereHas('activePlan');
                } elseif ($status === 'inactive') {
                    $query->whereDoesntHave('activePlan');
                }
            })
            ->orderBy('name')
            ->paginate(20);

        // Calcular estatísticas para cada aluno
        $students->getCollection()->transform(function ($student) {
            $student->stats = $this->calculateStudentStats($student);
            return $student;
        });

        return view('admin.students.index', compact('students', 'search', 'status'));
    }

    /**
     * Detalhes de um aluno específico
     */
    public function show(User $student)
    {
        $student->load([
            'activePlan.plan', 
            'activePlan.creditLogs.creator', 
            'activePlan.creditLogs.booking',
            'workoutsAsStudent.instructor',
            'bioimpedanceMeasurements.measuredBy'
        ]);

        // Estatísticas detalhadas
        $stats = $this->calculateStudentStats($student);
        
        // Histórico de bookings
        $bookings = Booking::where('user_id', $student->id)
            ->with('schedule')
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->paginate(15);

        // Logs de créditos (últimos 50)
        $creditLogs = CreditLog::where('user_id', $student->id)
            ->with(['creator', 'booking'])
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        return view('admin.students.show', compact('student', 'stats', 'bookings', 'creditLogs'));
    }

    /**
     * Atribuir plano ao aluno
     */
    public function assignPlan(Request $request, User $student)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'starts_at' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            // Desativar planos ativos existentes
            \App\Models\UserPlan::where('user_id', $student->id)
                ->where('status', 'active')
                ->update(['status' => 'cancelled']);

            $plan = \App\Models\Plan::findOrFail($validated['plan_id']);
            $startsAt = \Carbon\Carbon::parse($validated['starts_at']);
            $endsAt = $startsAt->copy()->addDays($plan->duration_days);

            // Criar novo plano
            $userPlan = \App\Models\UserPlan::create([
                'user_id' => $student->id,
                'plan_id' => $plan->id,
                'credits_remaining' => $plan->type === 'unlimited' ? 999 : $plan->credits,
                'extra_credits' => 0,
                'total_credits_used' => 0,
                'status' => 'active',
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
            ]);

            // Criar log de créditos inicial
            \App\Models\CreditLog::logAction(
                userPlanId: $userPlan->id,
                userId: $student->id,
                createdBy: Auth::id(),
                actionType: 'plan_assigned',
                creditType: 'regular',
                amount: $plan->type === 'unlimited' ? 999 : $plan->credits,
                balanceAfter: $plan->type === 'unlimited' ? 999 : $plan->credits,
                reason: "Plano {$plan->name} atribuído manualmente"
            );

            DB::commit();

            return back()->with('success', "✅ Plano {$plan->name} atribuído com sucesso! Válido até " . $endsAt->format('d/m/Y'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao atribuir plano: ' . $e->getMessage());
        }
    }

    /**
     * Adicionar créditos extras
     */
    public function addExtraCredits(Request $request, User $student)
    {
        $validated = $request->validate([
            'amount' => 'required|integer|min:1|max:100',
            'reason' => 'required|string|max:500',
        ]);

        if (!$student->activePlan) {
            return back()->with('error', 'Aluno não possui plano ativo.');
        }

        DB::beginTransaction();
        try {
            $student->activePlan->addExtraCredits(
                $validated['amount'],
                $validated['reason'],
                Auth::id()
            );

            DB::commit();

            return back()->with('success', "✅ {$validated['amount']} crédito(s) extra adicionado(s) com sucesso!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao adicionar créditos: ' . $e->getMessage());
        }
    }

    /**
     * Adicionar observação ao plano
     */
    public function addObservation(Request $request, User $student)
    {
        $validated = $request->validate([
            'observation' => 'required|string|max:1000',
        ]);

        if (!$student->activePlan) {
            return back()->with('error', 'Aluno não possui plano ativo.');
        }

        DB::beginTransaction();
        try {
            $currentObs = $student->activePlan->observations ?? '';
            $userName = Auth::user()->name;
            $newObs = Carbon::now()->format('d/m/Y H:i') . ' - ' . $userName . ': ' . $validated['observation'];
            
            $student->activePlan->update([
                'observations' => $currentObs ? $currentObs . "\n\n" . $newObs : $newObs
            ]);

            DB::commit();

            return back()->with('success', '✅ Observação adicionada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao adicionar observação: ' . $e->getMessage());
        }
    }

    /**
     * Marcar presença manualmente
     */
    public function markAttendance(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:attended,absent',
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $booking->status;
            $booking->update(['status' => $validated['status']]);

            // Se mudou de absent para attended, não precisa devolver crédito
            // Se mudou de attended para absent, considerar política de devolução

            DB::commit();

            $statusLabel = $validated['status'] === 'attended' ? 'Presente' : 'Ausente';
            return back()->with('success', "✅ Status atualizado para: {$statusLabel}");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao atualizar status: ' . $e->getMessage());
        }
    }

    /**
     * Calcular estatísticas do aluno
     */
    private function calculateStudentStats(User $student): array
    {
        $bookings = Booking::where('user_id', $student->id)->get();
        
        $totalBookings = $bookings->count();
        $attended = $bookings->where('status', 'attended')->count();
        $absent = $bookings->where('status', 'absent')->count();
        $cancelled = $bookings->where('status', 'cancelled')->count();
        $booked = $bookings->where('status', 'booked')->count();

        // Calcular taxa de presença
        $attendanceRate = $totalBookings > 0 
            ? round(($attended / $totalBookings) * 100, 1) 
            : 0;

        // Créditos
        $regularCredits = $student->activePlan->credits_remaining ?? 0;
        $extraCredits = $student->activePlan->extra_credits ?? 0;
        $totalCredits = $regularCredits + $extraCredits;
        $usedCredits = $student->activePlan->total_credits_used ?? 0;

        // Próximas aulas
        $upcomingClasses = Booking::where('user_id', $student->id)
            ->where('status', 'booked')
            ->where('date', '>=', now()->format('Y-m-d'))
            ->orderBy('date')
            ->orderBy('time')
            ->take(5)
            ->get();

        // Última presença
        $lastAttendance = Booking::where('user_id', $student->id)
            ->where('status', 'attended')
            ->orderBy('date', 'desc')
            ->first();

        return [
            'total_bookings' => $totalBookings,
            'attended' => $attended,
            'absent' => $absent,
            'cancelled' => $cancelled,
            'booked' => $booked,
            'attendance_rate' => $attendanceRate,
            'regular_credits' => $regularCredits,
            'extra_credits' => $extraCredits,
            'total_credits' => $totalCredits,
            'used_credits' => $usedCredits,
            'upcoming_classes' => $upcomingClasses,
            'last_attendance' => $lastAttendance,
            'has_active_plan' => $student->activePlan !== null,
        ];
    }

    /**
     * Exportar dados do aluno (PDF ou Excel)
     */
    public function export(User $student, $format = 'pdf')
    {
        // TODO: Implementar exportação
        return back()->with('info', 'Funcionalidade de exportação em desenvolvimento.');
    }

    /**
     * Adicionar medição de bioimpedância
     */
    public function storeBioimpedance(Request $request, User $student)
    {
        $request->validate([
            'weight' => 'required|numeric|min:0|max:500',
            'height' => 'nullable|numeric|min:0|max:300',
            'body_fat_percentage' => 'nullable|numeric|min:0|max:100',
            'muscle_mass' => 'nullable|numeric|min:0|max:200',
            'water_percentage' => 'nullable|numeric|min:0|max:100',
            'visceral_fat' => 'nullable|numeric|min:0|max:50',
            'basal_metabolic_rate' => 'nullable|integer|min:0|max:5000',
            'protein_percentage' => 'nullable|numeric|min:0|max:100',
            'chest' => 'nullable|numeric|min:0|max:200',
            'waist' => 'nullable|numeric|min:0|max:200',
            'hip' => 'nullable|numeric|min:0|max:200',
            'arm' => 'nullable|numeric|min:0|max:100',
            'thigh' => 'nullable|numeric|min:0|max:100',
            'measurement_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string|max:1000'
        ]);

        // Calcular BMI se altura foi fornecida
        $bmi = null;
        if ($request->height && $request->weight) {
            $heightInMeters = $request->height / 100;
            $bmi = round($request->weight / ($heightInMeters * $heightInMeters), 2);
        }

        BioimpedanceMeasurement::create([
            'user_id' => $student->id,
            'measured_by' => Auth::id(),
            'weight' => $request->weight,
            'height' => $request->height,
            'bmi' => $bmi,
            'body_fat_percentage' => $request->body_fat_percentage,
            'muscle_mass' => $request->muscle_mass,
            'water_percentage' => $request->water_percentage,
            'visceral_fat' => $request->visceral_fat,
            'basal_metabolic_rate' => $request->basal_metabolic_rate,
            'protein_percentage' => $request->protein_percentage,
            'chest' => $request->chest,
            'waist' => $request->waist,
            'hip' => $request->hip,
            'arm' => $request->arm,
            'thigh' => $request->thigh,
            'measurement_date' => $request->measurement_date,
            'notes' => $request->notes
        ]);

        return back()->with('success', 'Medição de bioimpedância registrada com sucesso!');
    }
}
