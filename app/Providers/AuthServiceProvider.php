<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use App\Policies\BookingPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Booking::class => BookingPolicy::class,
        Payment::class => PaymentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gates para verificação de papéis
        Gate::define('admin-access', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('instructor-access', function (User $user) {
            return in_array($user->role, ['admin', 'instrutor']);
        });

        Gate::define('student-access', function (User $user) {
            return in_array($user->role, ['admin', 'aluno']);
        });

        // Gate para verificar plano ativo
        Gate::define('has-active-plan', function (User $user) {
            return $user->activePlan()->exists();
        });
    }
}
