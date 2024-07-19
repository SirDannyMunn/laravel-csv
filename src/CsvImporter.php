<?php

namespace Vitorccs\LaravelCsv;

use Vitorccs\LaravelCsv\Entities\CsvConfig;
use Vitorccs\LaravelCsv\Services\ImportableService;

class CsvImporter
{
    /**
     * @var ImportableService
     */
    private ImportableService $service;

    /**
     * @param ImportableService $service
     */
    public function __construct(ImportableService $service)
    {
        $this->service = $service;
    }

    /**
     * @return CsvConfig
     */
    public function getConfig(): CsvConfig
    {
        return $this->service->getConfig();
    }

    /**
     * @param CsvConfig $config
     */
    public function setConfig(CsvConfig $config): void
    {
        $this->service->setConfig($config);
    }

    /**
     * @param object $importable
     * @return int
     */
    public function count(object $importable): int
    {
        return $this->service->count($importable);
    }

    /**
     * @param object $importable
     * @return array
     */
    public function getArray(object $importable): array
    {
        return $this->service->getArray($importable);
    }

    /**
     * @param object $importable
     * @param callable(array,int):void $callable
     * @param int|null $size
     * @return void
     */
    public function chunkArray(object   $importable,
                               callable $callable,
                               ?int     $size): void
    {
        $this->service->chunkArray($importable, $callable, $size);
    }
}
