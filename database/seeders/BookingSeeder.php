<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::where('role', 'aluno')->get();
        $schedules = \App\Models\Schedule::where('status', 'open')->get();
        foreach ($schedules->take(20) as $schedule) {
            $alunos = $users->random(3);
            foreach ($alunos as $aluno) {
                \App\Models\Booking::create([
                    'schedule_id' => $schedule->id,
                    'user_id' => $aluno->id,
                    'created_by' => $aluno->id,
                    'status' => 'booked',
                ]);
            }
        }
    }
}
