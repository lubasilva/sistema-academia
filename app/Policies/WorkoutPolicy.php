<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workout;
use Illuminate\Auth\Access\Response;

class WorkoutPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Todos podem ver a listagem (filtrada no controller)
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Workout $workout): bool
    {
        // Admin/Master/Instructor podem ver tudo, alunos só seus próprios treinos
        return in_array($user->role, ['admin', 'master', 'instructor']) || 
               $workout->student_id === $user->id ||
               $workout->instructor_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Admin, master e instructor podem criar treinos
        return in_array($user->role, ['admin', 'master', 'instructor']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Workout $workout): bool
    {
        // Admin/Master/Instructor e o instrutor que criou
        return in_array($user->role, ['admin', 'master']) || 
               $workout->instructor_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Workout $workout): bool
    {
        // Admin/Master podem deletar qualquer treino, instructor só o que criou
        return in_array($user->role, ['admin', 'master']) ||
               $workout->instructor_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Workout $workout): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Workout $workout): bool
    {
        return $user->role === 'admin';
    }
}
