<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $open = '06:00';
        $close = '22:00';
        $slotMinutes = 60;
        $startDate = now()->startOfWeek();
        $endDate = now()->endOfWeek();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            for ($hour = (int)substr($open,0,2); $hour < (int)substr($close,0,2); $hour++) {
                $starts_at = $date->copy()->setTime($hour, 0);
                $ends_at = $starts_at->copy()->addMinutes($slotMinutes);
                \App\Models\Schedule::create([
                    'starts_at' => $starts_at,
                    'ends_at' => $ends_at,
                    'status' => 'open',
                ]);
            }
        }
    }
}
