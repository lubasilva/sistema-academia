<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Schedule;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only create schedules if none exist for this week
        $startDate = now()->startOfWeek();
        if (Schedule::where('starts_at', '>=', $startDate)->exists()) {
            return;
        }

        $open = '06:00';
        $close = '22:00';
        $slotMinutes = 60;
        $endDate = now()->endOfWeek();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            for ($hour = (int)substr($open,0,2); $hour < (int)substr($close,0,2); $hour++) {
                $starts_at = $date->copy()->setTime($hour, 0);
                $ends_at = $starts_at->copy()->addMinutes($slotMinutes);
                Schedule::firstOrCreate(
                    [
                        'starts_at' => $starts_at,
                        'ends_at' => $ends_at,
                    ],
                    [
                        'status' => 'open',
                    ]
                );
            }
        }
    }
}
