<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use Illuminate\Http\Request;

class ExerciseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $muscleGroup = $request->get('muscle_group');
        $status = $request->get('status', 'all');

        $exercises = Exercise::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($muscleGroup, function ($query, $muscleGroup) {
                $query->where('muscle_group', $muscleGroup);
            })
            ->when($status !== 'all', function ($query) use ($status) {
                if ($status === 'active') {
                    $query->where('is_active', true);
                } else {
                    $query->where('is_active', false);
                }
            })
            ->orderBy('muscle_group')
            ->orderBy('name')
            ->paginate(20);

        $muscleGroups = Exercise::getMuscleGroups();

        return view('exercises.index', compact('exercises', 'search', 'muscleGroup', 'status', 'muscleGroups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $muscleGroups = Exercise::getMuscleGroups();
        $categories = Exercise::getCategories();
        
        return view('exercises.create', compact('muscleGroups', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'muscle_group' => 'required|string',
            'category' => 'nullable|string',
            'equipment' => 'nullable|string',
            'image_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Exercise::create($validated);

        return redirect()->route('exercises.index')
            ->with('success', 'Exercício criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Exercise $exercise)
    {
        $exercise->load('workoutExercises.workout.student');
        
        return view('exercises.show', compact('exercise'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exercise $exercise)
    {
        $muscleGroups = Exercise::getMuscleGroups();
        $categories = Exercise::getCategories();
        
        return view('exercises.edit', compact('exercise', 'muscleGroups', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exercise $exercise)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'muscle_group' => 'required|string',
            'category' => 'nullable|string',
            'equipment' => 'nullable|string',
            'image_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $exercise->update($validated);

        return redirect()->route('exercises.index')
            ->with('success', 'Exercício atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exercise $exercise)
    {
        // Verificar se o exercício está sendo usado em algum treino
        if ($exercise->workoutExercises()->count() > 0) {
            return back()->with('error', 'Não é possível excluir este exercício pois ele está sendo usado em treinos. Você pode desativá-lo.');
        }

        $exercise->delete();

        return redirect()->route('exercises.index')
            ->with('success', 'Exercício excluído com sucesso!');
    }
}
