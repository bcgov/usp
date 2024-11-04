<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Institution\InstitutionAttestationsDetails;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (config('app.env') === 'production' || config('app.env') === 'development') {
            $this->app['request']->server->set('HTTPS', 'on');
        }

        $this->app->bind('institution-attestations-details', function ($app) {
            return new InstitutionAttestationsDetails();
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
