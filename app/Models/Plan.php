<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Plan extends Model
{
    /** @use HasFactory<\Database\Factories\PlanFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'frequency_per_week',
        'billing_cycle',
        'price_cents',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Accessor para retornar preço em reais
    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->price_cents / 100,
        );
    }

    // Helper para nome do ciclo em português
    public function getBillingCycleNameAttribute(): string
    {
        return match($this->billing_cycle) {
            'monthly' => 'Mensal',
            'quarterly' => 'Trimestral',
            'semiannual' => 'Semestral',
            'annual' => 'Anual',
            default => $this->billing_cycle,
        };
    }

    // Helper para duração em dias
    public function getDurationDaysAttribute(): int
    {
        return match($this->billing_cycle) {
            'monthly' => 30,
            'quarterly' => 90,
            'semiannual' => 180,
            'annual' => 365,
            default => 30,
        };
    }
}
