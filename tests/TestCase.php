<?php

namespace Vitorccs\LaravelCsv\Tests;

use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Vitorccs\LaravelCsv\Facades\CsvImporter;
use Vitorccs\LaravelCsv\Helpers\CsvHelper;
use Vitorccs\LaravelCsv\ServiceProviders\CsvServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected string $filename;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filename = uniqid() . 'csv';
    }

    protected function getPackageProviders($app)
    {
        return [
            CsvServiceProvider::class
        ];
    }

    /**
     * Get application timezone.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return string|null
     */
    protected function getApplicationTimezone($app)
    {
        return 'UTC';
    }

    public function getFromDisk(string $filename,
                                bool   $cleanUtf8Bom = true): string
    {
        $csvConfig = CsvImporter::getConfig();
        $contents = Storage::disk($csvConfig->disk)->get($filename) ?: '';

        // remove empty line break
        $contents = preg_replace('/\s$/', '', $contents);

        if ($cleanUtf8Bom) {
            $contents = str_replace(CsvHelper::getBom(), '', $contents);
        }

        Storage::disk($csvConfig->disk)->delete($filename);

        return $contents;
    }

    public function getFromDiskArray(string $filename,
                                     bool   $cleanUtf8Bom = true): array
    {
        $contents = $this->getFromDisk($filename);
        return explode("\n", $contents);
    }

    /**
     * @param $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app->useStoragePath(realpath(__DIR__ . '/Data/Storage'));

        $app['config']->set('app.debug', env('APP_DEBUG', true));

        $app['config']->set('filesystems.default', 'local');
        $app['config']->set('filesystems.disks.local.root', realpath(__DIR__ . '/Data/Storage'));

        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => env('DB_DRIVER', 'sqlite'),
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT'),
            'database' => env('DB_DATABASE', ':memory:'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'prefix' => env('DB_PREFIX')
        ]);
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/Data/Database/Migrations');

        // Provides support for the previous generation of Laravel factories (<= 7.x) for Laravel 8.x+.
        $this->withFactories(__DIR__ . '/Data/Database/Factories');
    }
}
