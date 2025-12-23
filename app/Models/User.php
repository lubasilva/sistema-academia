<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'asaas_customer_id',
        'telegram_chat_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function userPlans()
    {
        return $this->hasMany(UserPlan::class);
    }

    public function activePlan()
    {
        return $this->hasOne(UserPlan::class)
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->latest();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    // Relacionamentos para Treinos
    public function workoutsAsStudent()
    {
        return $this->hasMany(Workout::class, 'student_id');
    }

    public function workoutsAsInstructor()
    {
        return $this->hasMany(Workout::class, 'instructor_id');
    }

    public function workoutExecutions()
    {
        return $this->hasMany(WorkoutExecution::class);
    }

    // Relacionamentos para Bioimpedância
    public function bioimpedanceMeasurements()
    {
        return $this->hasMany(BioimpedanceMeasurement::class)->orderBy('measurement_date', 'desc');
    }

    public function latestBioimpedance()
    {
        return $this->hasOne(BioimpedanceMeasurement::class)->latestOfMany('measurement_date');
    }
}
