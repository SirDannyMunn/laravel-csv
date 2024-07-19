<?php

namespace Vitorccs\LaravelCsv\Concerns\Importables;

use Vitorccs\LaravelCsv\Entities\CsvConfig;
use Vitorccs\LaravelCsv\Facades\CsvImporter;

trait Importable
{
    /**
     * @return int|null
     */
    public function limit(): ?int
    {
        return null;
    }

    /**
     * @return CsvConfig
     */
    public function getConfig(): CsvConfig
    {
        return CsvImporter::getConfig();
    }

    /**
     * @param CsvConfig $config
     */
    public function setConfig(CsvConfig $config): void
    {
        CsvImporter::setConfig($config);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return CsvImporter::count($this);
    }

    /**
     * @param callable(array,int):void $callable
     * @param int|null $size
     * @return void
     */
    public function chunkArray(callable $callable,
                               ?int     $size = null): void
    {
        CsvImporter::chunkArray($this, $callable, $size);
    }

    /**
     * @return array
     */
    public function getArray(): array
    {
        return CsvImporter::getArray($this);
    }
}
