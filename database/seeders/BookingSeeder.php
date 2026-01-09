<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Booking;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only create bookings if none exist yet
        if (Booking::exists()) {
            return;
        }

        $users = User::where('role', 'aluno')->get();
        $schedules = Schedule::where('status', 'open')->get();
        
        foreach ($schedules->take(20) as $schedule) {
            $alunos = $users->random(min(3, $users->count()));
            foreach ($alunos as $aluno) {
                Booking::firstOrCreate(
                    [
                        'schedule_id' => $schedule->id,
                        'user_id' => $aluno->id,
                    ],
                    [
                        'created_by' => $aluno->id,
                        'status' => 'booked',
                    ]
                );
            }
        }
    }
}
