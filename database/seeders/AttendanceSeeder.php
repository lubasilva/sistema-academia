<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Attendance;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only create attendances if none exist yet
        if (Attendance::exists()) {
            return;
        }

        $bookings = Booking::all();
        foreach ($bookings->take(10) as $booking) {
            Attendance::firstOrCreate(
                ['booking_id' => $booking->id],
                [
                    'present' => true,
                    'marked_by' => $booking->created_by,
                ]
            );
        }
    }
}
