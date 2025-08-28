<?php

namespace Ssh521\LaravelFileManager;

use Illuminate\Support\ServiceProvider;

class FileManagerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Merge package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/file-manager.php', 'file-manager');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        //php artisan vendor:publish --provider="Ssh521\LaravelFileManager\FileManagerServiceProvider"
        //php artisan vendor:publish --tag=file-manager-config
        //php artisan vendor:publish --tag=file-manager-views
        //php artisan vendor:publish --tag=file-manager-assets
        
        // Load package routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // Load package views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'file-manager');

        // Publish assets when running in console
        if ($this->app->runningInConsole()) {
            // Publish configuration
            $this->publishes([
                __DIR__ . '/../config/file-manager.php' => config_path('file-manager.php'),
            ], 'file-manager-config');

            // Publish views
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/file-manager'),
            ], 'file-manager-views');
        }
    }
}