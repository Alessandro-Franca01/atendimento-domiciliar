<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Models\Professional;
use Illuminate\Auth\EloquentUserProvider;

class ProfessionalAuthProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Auth::provider('professional', function ($app, array $config) {
            return new EloquentUserProvider($app['hash'], Professional::class);
        });
    }
}