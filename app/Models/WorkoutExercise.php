<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkoutExercise extends Model
{
    protected $fillable = [
        'workout_id',
        'exercise_id',
        'order_in_workout',
        'sets',
        'reps',
        'initial_weight',
        'rest_seconds',
        'notes',
    ];

    protected $casts = [
        'initial_weight' => 'decimal:2',
    ];

    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    public function executions(): HasMany
    {
        return $this->hasMany(WorkoutExecution::class);
    }

    /**
     * Pega a última execução deste exercício
     */
    public function lastExecution()
    {
        return $this->executions()->latest('execution_date')->first();
    }

    /**
     * Calcula a progressão de peso ao longo do tempo
     */
    public function getWeightProgression(): array
    {
        return $this->executions()
            ->orderBy('execution_date')
            ->get()
            ->map(function($execution) {
                return [
                    'date' => $execution->execution_date,
                    'weight' => $execution->average_weight,
                    'volume' => $execution->total_volume,
                ];
            })
            ->toArray();
    }

    /**
     * Formatar tempo de descanso
     */
    public function getFormattedRestTime(): string
    {
        if ($this->rest_seconds < 60) {
            return $this->rest_seconds . 's';
        }
        
        $minutes = floor($this->rest_seconds / 60);
        $seconds = $this->rest_seconds % 60;
        
        if ($seconds == 0) {
            return $minutes . 'min';
        }
        
        return $minutes . 'min' . $seconds . 's';
    }
}
