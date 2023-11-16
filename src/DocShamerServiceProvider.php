<?php

namespace Thewoods96\DocShamer;

use Thewoods96\DocShamer\DocShamer;
use Illuminate\Support\ServiceProvider;

class DocShamerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('doc-shamer.php'),
        ], 'config');

        // Registering package commands.
        $this->commands([]);
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'doc-shamer');

        // Register the main class to use with the facade
        $this->app->singleton('doc-shamer', function () {
            return new DocShamer;
        });
    }
}
