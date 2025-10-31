<?php

namespace App\Providers;

use App\Repository\UserRepository;
use App\Repository\UserRepositoryInRD;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;
use App\Repository\ValidationRepository;
use App\Repository\ValidationRepositoryInRD;
use App\Services\ValidationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind Repository interface to its implementation
        $this->app->bind(UserRepository::class, UserRepositoryInRD::class);
        $this->app->bind(ValidationRepository::class, ValidationRepositoryInRD::class);

        
        // Register UserService as singleton
        $this->app->singleton(UserService::class, function ($app) {
            return new UserService($app->make(UserRepository::class), $app->make(ValidationService::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}