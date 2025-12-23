<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'instructor', 'student']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Booking $booking): bool
    {
        return $user->role === 'admin' || $user->id === $booking->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Admin e instrutor podem criar reservas para qualquer aluno
        if (in_array($user->role, ['admin', 'instructor'])) {
            return true;
        }
        
        // Estudante só pode criar se tiver plano ativo
        return $user->role === 'student' && $user->activePlan()->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Booking $booking): bool
    {
        return $user->role === 'admin' || ($user->id === $booking->user_id && $booking->status !== 'cancelled');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Booking $booking): bool
    {
        return $user->role === 'admin' || ($user->id === $booking->user_id && $booking->date >= now()->toDateString());
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Booking $booking): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Booking $booking): bool
    {
        return $user->role === 'admin';
    }
}
