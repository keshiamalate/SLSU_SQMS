<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
        // Bind matching services
        $this->app->singleton(\App\Services\EligibilityEngine::class);
        $this->app->singleton(\App\Services\DuplicateFilter::class);
        $this->app->singleton(\App\Services\MatchingScorer::class);
        $this->app->singleton(\App\Services\MatchingService::class);
        $this->app->singleton(\App\Services\MlApiClient::class);
    }
}
