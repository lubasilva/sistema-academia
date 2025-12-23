<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkoutExecution extends Model
{
    protected $fillable = [
        'workout_exercise_id',
        'user_id',
        'execution_date',
        'sets_data',
        'notes',
        'total_volume',
        'average_weight',
        'duration_minutes',
    ];

    protected $casts = [
        'execution_date' => 'date',
        'sets_data' => 'array',
        'average_weight' => 'decimal:2',
    ];

    public function workoutExercise(): BelongsTo
    {
        return $this->belongsTo(WorkoutExercise::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calcula automaticamente volume total e peso médio
     */
    public function calculateStats(): void
    {
        if (!$this->sets_data) return;
        
        $totalWeight = 0;
        $totalReps = 0;
        $completedSets = 0;
        
        foreach ($this->sets_data as $set) {
            if ($set['completed'] ?? false) {
                $weight = $set['weight'] ?? 0;
                $reps = $set['reps'] ?? 0;
                
                $totalWeight += $weight;
                $totalReps += $reps;
                $completedSets++;
                
                $this->total_volume += $weight * $reps;
            }
        }
        
        if ($completedSets > 0) {
            $this->average_weight = $totalWeight / $completedSets;
        }
    }

    /**
     * Verifica se todas as séries foram completadas
     */
    public function isFullyCompleted(): bool
    {
        if (!$this->sets_data) return false;
        
        foreach ($this->sets_data as $set) {
            if (!($set['completed'] ?? false)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Conta quantas séries foram completadas
     */
    public function getCompletedSetsCount(): int
    {
        if (!$this->sets_data) return 0;
        
        return collect($this->sets_data)->where('completed', true)->count();
    }
}
