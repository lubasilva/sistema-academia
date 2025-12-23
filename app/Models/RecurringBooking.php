<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecurringBooking extends Model
{
    protected $fillable = [
        'user_id',
        'day_of_week',
        'time',
        'is_active',
        'last_created_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_created_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
    
    /**
     * Desativa este padrão recorrente
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }
    
    /**
     * Retorna o nome do dia em português
     */
    public function getDayNameAttribute(): string
    {
        $days = [
            'monday' => 'Segunda-feira',
            'tuesday' => 'Terça-feira',
            'wednesday' => 'Quarta-feira',
            'thursday' => 'Quinta-feira',
            'friday' => 'Sexta-feira',
            'saturday' => 'Sábado',
            'sunday' => 'Domingo',
        ];
        
        return $days[$this->day_of_week] ?? $this->day_of_week;
    }
}
