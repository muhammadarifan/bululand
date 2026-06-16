<?php

namespace App\Providers;

use App\Services\Gowa\GowaMessageSender;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(GowaMessageSender::class, static fn(): GowaMessageSender => GowaMessageSender::fromConfig());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (!$this->app->environment('local')) {
            URL::forceScheme('https');
        }
    }
}
