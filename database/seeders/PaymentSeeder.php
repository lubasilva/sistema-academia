<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserPlan;
use App\Models\Payment;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only create payments if none exist yet
        if (Payment::exists()) {
            return;
        }

        $userPlans = UserPlan::all();
        foreach ($userPlans->take(10) as $userPlan) {
            Payment::firstOrCreate(
                [
                    'user_id' => $userPlan->user_id,
                    'user_plan_id' => $userPlan->id,
                ],
                [
                    'type' => 'subscription',
                    'amount_cents' => 9900,
                    'status' => 'paid',
                ]
            );
        }
    }
}
