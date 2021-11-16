<?php

namespace Dimafe6\GoogleCalendar;

use Dimafe6\GoogleCalendar\Services\GoogleService;
use Illuminate\Support\ServiceProvider;

class GoogleCalendarServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'dimafe6');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'dimafe6');
         $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

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
        $this->mergeConfigFrom(__DIR__.'/../config/googlecalendar.php', 'googlecalendar');

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
            __DIR__.'/../config/googlecalendar.php' => config_path('googlecalendar.php'),
        ], 'googlecalendar.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/dimafe6'),
        ], 'googlecalendar.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/dimafe6'),
        ], 'googlecalendar.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/dimafe6'),
        ], 'googlecalendar.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
