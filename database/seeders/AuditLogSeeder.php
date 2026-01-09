<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\AuditLog;

class AuditLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only create audit logs if none exist yet
        if (AuditLog::exists()) {
            return;
        }

        $users = User::all();
        $actions = [
            'login', 'logout', 'create_booking', 'cancel_booking', 'update_profile', 'payment_created', 'plan_changed', 'attendance_marked', 'settings_updated', 'admin_report_generated'
        ];
        foreach ($users->take(10) as $user) {
            foreach ($actions as $action) {
                AuditLog::create([
                    'user_id' => $user->id,
                    'action' => $action,
                    'description' => "Ação {$action} executada por {$user->name}",
                ]);
            }
        }
    }
}
