<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userPlans = \App\Models\UserPlan::all();
        foreach ($userPlans->take(10) as $userPlan) {
            \App\Models\Payment::create([
                'user_id' => $userPlan->user_id,
                'user_plan_id' => $userPlan->id,
                'type' => 'subscription',
                'amount_cents' => 9900,
                'status' => 'paid',
            ]);
        }
    }
}
