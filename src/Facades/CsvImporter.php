<?php

namespace Vitorccs\LaravelCsv\Facades;

use Illuminate\Support\Facades\Facade;
use Vitorccs\LaravelCsv\Entities\CsvConfig;

/**
 * @method static CsvConfig getConfig()
 * @method static void setConfig(CsvConfig $config)
 * @method static int count(object $importable)
 * @method static void chunkArray(object $importable, callable $callable, ?int $size = null)
 * @method static array getArray(object $importable)
 */
class CsvImporter extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'csv_importer';
    }
}
