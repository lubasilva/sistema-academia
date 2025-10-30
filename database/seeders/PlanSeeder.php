<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => '1x/semana Mensal',
                'slug' => '1x-mensal',
                'frequency_per_week' => 1,
                'billing_cycle' => 'monthly',
                'price_cents' => 9900,
                'is_active' => true,
            ],
            [
                'name' => '2x/semana Trimestral',
                'slug' => '2x-trimestral',
                'frequency_per_week' => 2,
                'billing_cycle' => 'quarterly',
                'price_cents' => 24900,
                'is_active' => true,
            ],
            [
                'name' => '3x/semana Semestral',
                'slug' => '3x-semestral',
                'frequency_per_week' => 3,
                'billing_cycle' => 'semiannual',
                'price_cents' => 44900,
                'is_active' => true,
            ],
            [
                'name' => '5x/semana Anual',
                'slug' => '5x-anual',
                'frequency_per_week' => 5,
                'billing_cycle' => 'annual',
                'price_cents' => 79900,
                'is_active' => true,
            ],
        ];
        foreach ($plans as $plan) {
            \App\Models\Plan::create($plan);
        }
    }
}
