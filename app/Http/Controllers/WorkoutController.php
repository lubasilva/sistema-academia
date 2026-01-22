<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use App\Models\WorkoutExecution;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class WorkoutController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'admin' || $user->role === 'master') {
            // Admin/Master vê treinos de todos os alunos
            $workouts = Workout::with(['student', 'instructor', 'exercises.exercise'])
                ->latest()
                ->paginate(15);
        } else {
            // Alunos vêem apenas seus próprios treinos
            $workouts = Workout::with(['instructor', 'exercises.exercise'])
                ->where('student_id', $user->id)
                ->latest()
                ->paginate(15);
        }
        
        return view('workouts.index', compact('workouts'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Workout::class);
        
        $exercises = Exercise::active()
            ->orderBy('muscle_group')
            ->orderBy('name')
            ->get()
            ->groupBy('muscle_group');
        
        $students = User::where('role', 'aluno')->orderBy('name')->get();
        $selectedStudentId = $request->get('student_id');
        
        return view('workouts.create', compact('exercises', 'students', 'selectedStudentId'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Workout::class);
        
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:strength,cardio,functional',
            'frequency_per_week' => 'required|integer|min:1|max:7',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'exercises' => 'required|array|min:1',
            'exercises.*.exercise_id' => 'required|exists:exercises,id',
            'exercises.*.sets' => 'required|integer|min:1',
            'exercises.*.reps' => 'required|string',
            'exercises.*.initial_weight' => 'nullable|numeric|min:0',
            'exercises.*.rest_seconds' => 'required|integer|min:10',
            'exercises.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $workout = Workout::create([
                'student_id' => $validated['student_id'],
                'instructor_id' => Auth::id(),
                'name' => $validated['name'],
                'description' => $validated['description'],
                'type' => $validated['type'],
                'frequency_per_week' => $validated['frequency_per_week'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ]);

            foreach ($validated['exercises'] as $index => $exerciseData) {
                WorkoutExercise::create([
                    'workout_id' => $workout->id,
                    'exercise_id' => $exerciseData['exercise_id'],
                    'order_in_workout' => $index + 1,
                    'sets' => $exerciseData['sets'],
                    'reps' => $exerciseData['reps'],
                    'initial_weight' => $exerciseData['initial_weight'],
                    'rest_seconds' => $exerciseData['rest_seconds'],
                    'notes' => $exerciseData['notes'],
                ]);
            }

            DB::commit();
            
            return redirect()->route('workouts.show', $workout)
                ->with('success', 'Treino criado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao criar treino: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Criar treino simples via modal (com exercícios opcionais)
     */
    public function storeSimple(Request $request)
    {
        $this->authorize('create', Workout::class);
        
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'goal' => 'nullable|string|in:hipertrofia,forca,resistencia,perda_peso,condicionamento',
            'exercises' => 'nullable|array',
            'exercises.*.name' => 'required_with:exercises|string|max:255',
            'exercises.*.sets' => 'required_with:exercises|integer|min:1|max:10',
            'exercises.*.reps' => 'required_with:exercises|string|max:50',
            'exercises.*.weight' => 'nullable|numeric|min:0',
            'exercises.*.rest' => 'required_with:exercises|integer|min:10',
            'exercises.*.notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $workout = Workout::create([
                'student_id' => $validated['student_id'],
                'instructor_id' => Auth::id(),
                'name' => $validated['name'],
                'description' => $validated['description'],
                'type' => 'strength', // Default
                'goal' => $validated['goal'] ?? 'hipertrofia',
                'frequency_per_week' => 3, // Default
                'start_date' => now(),
                'is_active' => true,
            ]);

            // Se há exercícios, criar exercícios e relacionamentos
            if (!empty($validated['exercises'])) {
                foreach ($validated['exercises'] as $index => $exerciseData) {
                    // Procurar exercício existente ou criar um novo
                    $exercise = Exercise::firstOrCreate(
                        ['name' => $exerciseData['name']],
                        [
                            'muscle_group' => 'Geral', // Default
                            'category' => 'Máquina', // Default
                            'instructions' => 'Exercício adicionado pelo usuário.',
                        ]
                    );

                    // Criar relacionamento do exercício com o treino
                    WorkoutExercise::create([
                        'workout_id' => $workout->id,
                        'exercise_id' => $exercise->id,
                        'order_in_workout' => $index + 1,
                        'sets' => $exerciseData['sets'],
                        'reps' => $exerciseData['reps'],
                        'initial_weight' => $exerciseData['weight'] ?? 0,
                        'rest_seconds' => $exerciseData['rest'],
                        'notes' => $exerciseData['notes'],
                    ]);
                }
            }

            DB::commit();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Treino criado com sucesso!',
                    'workout' => $workout
                ]);
            }
            
            return back()->with('success', 'Treino criado com sucesso!' . 
                (empty($validated['exercises']) ? ' Agora você pode adicionar exercícios.' : ''));
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar treino: ' . $e->getMessage()
                ], 422);
            }
            
            return back()->with('error', 'Erro ao criar treino: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Workout $workout)
    {
        $this->authorize('view', $workout);
        
        try {
            $workout->load([
                'student', 
                'instructor', 
                'exercises.exercise', 
                'exercises.executions'
            ]);
            
            return view('workouts.show', compact('workout'));
        } catch (\Exception $e) {
            Log::error('Erro ao visualizar treino: ' . $e->getMessage(), [
                'workout_id' => $workout->id,
                'user_id' => Auth::id(),
            ]);
            
            return redirect()->route('workouts.index')
                ->with('error', 'Erro ao visualizar treino: ' . $e->getMessage());
        }
    }

    public function edit(Workout $workout)
    {
        $this->authorize('update', $workout);
        
        $exercises = Exercise::active()
            ->orderBy('muscle_group')
            ->orderBy('name')
            ->get()
            ->groupBy('muscle_group');
        
        $students = User::where('role', 'aluno')->orderBy('name')->get();
        $workout->load('exercises.exercise');
        
        return view('workouts.edit', compact('workout', 'exercises', 'students'));
    }

    public function update(Request $request, Workout $workout)
    {
        $this->authorize('update', $workout);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:strength,cardio,functional',
            'frequency_per_week' => 'required|integer|min:1|max:7',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $workout->update($validated);
        
        return redirect()->route('workouts.show', $workout)
            ->with('success', 'Treino atualizado com sucesso!');
    }

    public function destroy(Workout $workout)
    {
        $this->authorize('delete', $workout);
        
        $workout->delete();
        
        return redirect()->route('workouts.index')
            ->with('success', 'Treino excluído com sucesso!');
    }
}
