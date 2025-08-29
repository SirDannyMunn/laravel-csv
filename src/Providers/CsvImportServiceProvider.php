<?php

namespace SirDannyMunn\CsvImport\Providers;

use Illuminate\Support\ServiceProvider;
use SirDannyMunn\CsvImport\Services\ImportService;
use SirDannyMunn\CsvImport\Services\ExportService;
use SirDannyMunn\CsvImport\Services\ModelResolver;
use SirDannyMunn\CsvImport\Services\MappingService;
use SirDannyMunn\CsvImport\Services\ValidationService;
use SirDannyMunn\CsvImport\Services\CsvProcessor;
use SirDannyMunn\CsvImport\Helpers\NsHelper;

class CsvImportServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register package config
        $this->mergeConfigFrom(
            __DIR__.'/../Config/csv-importer.php', 'csv-importer'
        );

        // Register services as singletons
        $this->app->singleton(ImportService::class);
        $this->app->singleton(ExportService::class);
        $this->app->singleton(ModelResolver::class);
        $this->app->singleton(MappingService::class);
        $this->app->singleton(ValidationService::class);
        $this->app->singleton(CsvProcessor::class);
        $this->app->singleton(NsHelper::class);

        // Register NS helper if not already defined
        $this->registerNsHelper();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../Config/csv-importer.php' => config_path('csv-importer.php'),
        ], 'csv-importer-config');

        // Publish migrations
        $this->publishes([
            __DIR__.'/../../database/migrations/' => database_path('migrations'),
        ], 'csv-importer-migrations');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }

    /**
     * Register NS helper function if not already defined
     */
    protected function registerNsHelper(): void
    {
        if (!function_exists('ns')) {
            require_once __DIR__.'/../Helpers/ns_helper.php';
        }
    }
    
    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            ImportService::class,
            ExportService::class,
            ModelResolver::class,
            MappingService::class,
            ValidationService::class,
            CsvProcessor::class,
            NsHelper::class,
        ];
    }
}