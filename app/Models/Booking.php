<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    /** @use HasFactory<\Database\Factories\BookingFactory> */
    use HasFactory;

    protected $fillable = [
        'schedule_id', // Mantido para compatibilidade com sistema antigo
        'user_id',
        'date',
        'time', // Novo campo para horário direto
        'created_by',
        'status',
        'recurring_booking_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function recurringBooking()
    {
        return $this->belongsTo(RecurringBooking::class);
    }
    
    /**
     * Verifica se esta reserva faz parte de uma recorrência
     */
    public function isRecurring(): bool
    {
        return !is_null($this->recurring_booking_id);
    }
}
