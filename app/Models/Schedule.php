<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    /** @use HasFactory<\Database\Factories\ScheduleFactory> */
    use HasFactory;

    protected $fillable = [
        'starts_at',
        'ends_at',
        'capacity_override',
        'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getAvailableSpotsAttribute()
    {
        $capacity = $this->capacity_override ?? Setting::where('key', 'max_capacity_per_class')->value('value') ?? 10;
        $booked = $this->bookings()->whereIn('status', ['booked', 'attended'])->count();
        return max(0, $capacity - $booked);
    }

    public function getIsFullAttribute()
    {
        return $this->available_spots <= 0;
    }
}
