<?php

namespace SirDannyMunn\CsvImport;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class CsvImportServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Bind services to the container
        $this->app->singleton(Services\ImportService::class);
        $this->app->singleton(Services\ExportService::class);
        $this->app->singleton(Services\ValidationService::class);
        $this->app->singleton(Services\MappingService::class);

        // Register package configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../config/csv-import.php', 'csv-import'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Routes are loaded via pos/main/routes/web/csv-import.php
        // $this->loadRoutesFrom(__DIR__ . '/../routes/csv-import.php');
        
        // Load views if using blade templates
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'csv-import');
        
        // Publish configuration
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/csv-import.php' => config_path('csv-import.php'),
            ], 'csv-import-config');
        }

        // Register model configurations
        $this->registerModelConfigurations();
    }

    /**
     * Register model configurations for CSV import/export
     */
    protected function registerModelConfigurations()
    {
        // This allows other packages or the application to register models
        // that support CSV import/export
        $this->app['csv.models'] = $this->app['config']['csv-import.models'] ?? [];
    }
}