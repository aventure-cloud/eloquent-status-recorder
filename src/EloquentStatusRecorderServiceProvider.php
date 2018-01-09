<?php

namespace AventureCloud\EloquentStatusRecorder;

use Illuminate\Support\ServiceProvider;

class EloquentStatusRecorderServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Database migrations
        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations')
        ], 'migrations');

        // Configuration file
        $this->publishes([
            __DIR__ . '/../config/eloquent-status-recorder.php' => config_path('eloquent-status-recorder.php')
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}