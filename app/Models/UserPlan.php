<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPlan extends Model
{
    /** @use HasFactory<\Database\Factories\UserPlanFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'credits_remaining',
        'extra_credits',         // Créditos de reposição
        'total_credits_used',    // Total de créditos usados
        'observations',          // Observações do plano
        'status',
        'starts_at',
        'ends_at',
        'asaas_customer_id',
        'asaas_subscription_id',
        'asaas_invoice_id',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Logs de créditos
     */
    public function creditLogs()
    {
        return $this->hasMany(CreditLog::class);
    }

    /**
     * Total de créditos disponíveis (regulares + extras)
     */
    public function getTotalCreditsAttribute(): int
    {
        return ($this->credits_remaining ?? 0) + ($this->extra_credits ?? 0);
    }

    /**
     * Adicionar créditos extras com log
     */
    public function addExtraCredits(int $amount, string $reason, int $createdBy): void
    {
        $this->increment('extra_credits', $amount);
        $this->refresh();

        CreditLog::logAction(
            userPlanId: $this->id,
            userId: $this->user_id,
            createdBy: $createdBy,
            actionType: 'extra_credit_added',
            creditType: 'extra',
            amount: $amount,
            balanceAfter: $this->extra_credits,
            reason: $reason
        );
    }

    /**
     * Usar crédito (prioriza regular, depois extra) com log
     */
    public function useCredit(int $createdBy, ?int $bookingId = null): bool
    {
        if ($this->credits_remaining > 0) {
            // Usa crédito regular
            $this->decrement('credits_remaining');
            $this->increment('total_credits_used');
            $this->refresh();

            CreditLog::logAction(
                userPlanId: $this->id,
                userId: $this->user_id,
                createdBy: $createdBy,
                actionType: 'credit_used',
                creditType: 'regular',
                amount: -1,
                balanceAfter: $this->credits_remaining,
                reason: 'Crédito regular usado em agendamento',
                bookingId: $bookingId
            );

            return true;
        } elseif ($this->extra_credits > 0) {
            // Usa crédito extra
            $this->decrement('extra_credits');
            $this->increment('total_credits_used');
            $this->refresh();

            CreditLog::logAction(
                userPlanId: $this->id,
                userId: $this->user_id,
                createdBy: $createdBy,
                actionType: 'credit_used',
                creditType: 'extra',
                amount: -1,
                balanceAfter: $this->extra_credits,
                reason: 'Crédito extra (reposição) usado em agendamento',
                bookingId: $bookingId
            );

            return true;
        }

        return false;
    }

    /**
     * Devolver crédito (vira extra) com log
     */
    public function returnCredit(int $createdBy, string $reason = 'Cancelamento de aula', ?int $bookingId = null): void
    {
        // Créditos devolvidos viram extras (para reposição)
        $this->increment('extra_credits');
        $this->decrement('total_credits_used');
        $this->refresh();

        CreditLog::logAction(
            userPlanId: $this->id,
            userId: $this->user_id,
            createdBy: $createdBy,
            actionType: 'credit_returned',
            creditType: 'extra',
            amount: 1,
            balanceAfter: $this->extra_credits,
            reason: $reason,
            bookingId: $bookingId
        );
    }
}
