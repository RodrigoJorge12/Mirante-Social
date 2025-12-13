<?php

namespace App\Providers;

use App\Repository\UserRepository;
use App\Repository\UserRepositoryInRD;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;
use App\Repository\ValidationRepository;
use App\Repository\ValidationRepositoryInRD;
use App\Services\ValidationService;
use App\Repository\PersonalizedPageRepository;
use App\Repository\PersonalizedPageRepositoryInRD;
use App\Repository\SocialProjectRepository;
use App\Repository\SocialProjectRepositoryInRD;
use App\Repository\ReportRepository;
use App\Repository\ReportRepositoryInRD;
use App\Services\ReportService;
use App\Repository\ProjectValidationRepository;
use App\Repository\ProjectValidationRepositoryInRD;
use App\Services\ProjectVerificationService;

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
        $this->app->bind(PersonalizedPageRepository::class, PersonalizedPageRepositoryInRD::class);
        $this->app->bind(SocialProjectRepository::class, SocialProjectRepositoryInRD::class);
        $this->app->bind(ReportRepository::class, ReportRepositoryInRD::class);
        $this->app->bind(ProjectValidationRepository::class, ProjectValidationRepositoryInRD::class);

        // Register UserService as singleton
        $this->app->singleton(UserService::class, function ($app) {
            return new UserService($app->make(UserRepository::class), $app->make(ValidationService::class));
        });
        $this->app->singleton(ReportService::class, function ($app) {
            return new ReportService($app->make(ReportRepository::class), $app->make(SocialProjectRepository::class));
        });
        $this->app->singleton(ProjectVerificationService::class, function ($app) {
            return new ProjectVerificationService(
                $app->make(ProjectValidationRepository::class),
                $app->make(SocialProjectRepository::class)
            );
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
