<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_plan_id',
        'user_id',
        'created_by',
        'action_type',
        'credit_type',
        'amount',
        'balance_after',
        'reason',
        'booking_id',
    ];

    /**
     * Plano do usuário
     */
    public function userPlan(): BelongsTo
    {
        return $this->belongsTo(UserPlan::class);
    }

    /**
     * Aluno
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Quem executou a ação
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Reserva relacionada (quando aplicável)
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Helper para criar log de ação
     */
    public static function logAction(
        int $userPlanId,
        int $userId,
        int $createdBy,
        string $actionType,
        string $creditType,
        int $amount,
        int $balanceAfter,
        ?string $reason = null,
        ?int $bookingId = null
    ) {
        return self::create([
            'user_plan_id' => $userPlanId,
            'user_id' => $userId,
            'created_by' => $createdBy,
            'action_type' => $actionType,
            'credit_type' => $creditType,
            'amount' => $amount,
            'balance_after' => $balanceAfter,
            'reason' => $reason,
            'booking_id' => $bookingId,
        ]);
    }
}
