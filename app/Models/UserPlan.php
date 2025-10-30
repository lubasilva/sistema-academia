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
}
