<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (App::environment('production')) {
            return;
        }

        $this->call([
            OperatingHoursSeeder::class,
            UserSeeder::class,
            PlanSeeder::class,
            SettingSeeder::class,
            ScheduleSeeder::class,
            BookingSeeder::class,
            PaymentSeeder::class,
            AttendanceSeeder::class,
            AuditLogSeeder::class,
        ]);
    }
}
