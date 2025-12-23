<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exercise extends Model
{
    protected $fillable = [
        'name',
        'description',
        'muscle_group',
        'category',
        'equipment',
        'image_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function workoutExercises(): HasMany
    {
        return $this->hasMany(WorkoutExercise::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByMuscleGroup($query, $muscleGroup)
    {
        return $query->where('muscle_group', $muscleGroup);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Grupos musculares disponíveis
     */
    public static function getMuscleGroups(): array
    {
        return [
            'peitoral' => 'Peitoral',
            'costas' => 'Costas',
            'ombros' => 'Ombros',
            'biceps' => 'Bíceps',
            'triceps' => 'Tríceps',
            'pernas' => 'Pernas',
            'gluteos' => 'Glúteos',
            'core' => 'Core/Abdômen',
            'cardio' => 'Cardio',
        ];
    }

    /**
     * Categorias de exercício
     */
    public static function getCategories(): array
    {
        return [
            'strength' => 'Força',
            'cardio' => 'Cardio',
            'flexibility' => 'Flexibilidade',
            'functional' => 'Funcional',
        ];
    }
}
