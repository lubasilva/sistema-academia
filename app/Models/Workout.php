<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Workout extends Model
{
    protected $fillable = [
        'student_id',
        'instructor_id',
        'name',
        'description',
        'type',
        'frequency_per_week',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function exercises(): HasMany
    {
        return $this->hasMany(WorkoutExercise::class)->orderBy('order_in_workout');
    }

    public function executions(): HasMany
    {
        return $this->hasMany(WorkoutExecution::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Verifica se o treino está dentro do período ativo
     */
    public function isInPeriod(): bool
    {
        $now = Carbon::now()->toDateString();
        return $now >= $this->start_date->toDateString() && 
               ($this->end_date === null || $now <= $this->end_date->toDateString());
    }

    /**
     * Calcula a duração do treino em semanas
     */
    public function getDurationInWeeks(): ?int
    {
        if (!$this->end_date) return null;
        return $this->start_date->diffInWeeks($this->end_date);
    }
}
