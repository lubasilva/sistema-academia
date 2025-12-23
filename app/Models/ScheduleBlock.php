<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleBlock extends Model
{
    /** @use HasFactory<\Database\Factories\ScheduleBlockFactory> */
    use HasFactory;

    protected $fillable = [
        'date',
        'start_time',
        'end_time',
        'reason',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Relacionamento com o usuário que criou o bloqueio
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Verifica se um horário específico está bloqueado
     */
    public static function isBlocked($date, $time): bool
    {
        return self::where('date', $date)
            ->where('start_time', '<=', $time)
            ->where('end_time', '>', $time)
            ->exists();
    }

    /**
     * Obtém todos os bloqueios para uma data específica
     */
    public static function getBlocksForDate($date)
    {
        return self::where('date', $date)
            ->orderBy('start_time')
            ->get();
    }
}
