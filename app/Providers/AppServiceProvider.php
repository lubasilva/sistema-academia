<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Breeze\BreezeServiceProvider;
use Laravel\Pail\PailServiceProvider;
use Laravel\Sail\SailServiceProvider;
use NunoMaduro\Collision\Adapters\Laravel\CollisionServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(PailServiceProvider::class);
            $this->app->register(CollisionServiceProvider::class);
            $this->app->register(BreezeServiceProvider::class);
            $this->app->register(SailServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
    }
}
