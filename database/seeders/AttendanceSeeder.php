<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bookings = \App\Models\Booking::all();
        foreach ($bookings->take(10) as $booking) {
            \App\Models\Attendance::create([
                'booking_id' => $booking->id,
                'present' => true,
                'marked_by' => $booking->created_by,
            ]);
        }
    }
}
