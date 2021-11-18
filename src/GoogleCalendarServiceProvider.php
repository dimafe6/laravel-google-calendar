<?php

namespace Dimafe6\GoogleCalendar;

use Dimafe6\GoogleCalendar\Jobs\PeriodicSynchronizations;
use Dimafe6\GoogleCalendar\Jobs\RefreshWebhookSynchronizations;
use Dimafe6\GoogleCalendar\Services\GoogleService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

/**
 * Class GoogleCalendarServiceProvider
 *
 * @category PHP
 * @package  Dimafe6\GoogleCalendar
 * @author   Dmytro Feshchenko <dimafe2000@gmail.com>
 */
class GoogleCalendarServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        $this->app->booted(function () {
            /** @var Schedule $schedule */
            $schedule = $this->app->make(Schedule::class);

            if ($refreshJobExpression = config('googlecalendar.refresh_webhook_cron')) {
                $schedule->job(new RefreshWebhookSynchronizations())->cron($refreshJobExpression);
            }

            if ($syncJobExpression = config('googlecalendar.periodic_sync_cron')) {
                $schedule->job(new PeriodicSynchronizations())->cron($syncJobExpression);
            }
        });
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/googlecalendar.php', 'googlecalendar');

        // Register the service the package provides.
        $this->app->singleton('googlecalendar', function ($app) {
            return new GoogleService();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['googlecalendar'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__ . '/../config/googlecalendar.php' => config_path('googlecalendar.php'),
        ], 'googlecalendar.config');
    }
}
