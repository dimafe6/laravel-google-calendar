<?php

namespace Dimafe6\GoogleCalendar;

use Dimafe6\GoogleCalendar\Services\GoogleService;
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
        $this->loadRoutesFrom(__DIR__.'/../routes/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
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

        // Registering package commands.
        // $this->commands([]);
    }
}
