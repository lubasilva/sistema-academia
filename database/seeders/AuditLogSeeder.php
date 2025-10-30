<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::all();
        $actions = [
            'login', 'logout', 'create_booking', 'cancel_booking', 'update_profile', 'payment_created', 'plan_changed', 'attendance_marked', 'settings_updated', 'admin_report_generated'
        ];
        foreach ($users->take(10) as $user) {
            foreach ($actions as $action) {
                \App\Models\AuditLog::create([
                    'user_id' => $user->id,
                    'action' => $action,
                    'description' => "Ação {$action} executada por {$user->name}",
                ]);
            }
        }
    }
}
