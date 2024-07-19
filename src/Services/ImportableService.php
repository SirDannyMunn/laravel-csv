<?php

namespace Vitorccs\LaravelCsv\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Vitorccs\LaravelCsv\Concerns\Importables\FromContents;
use Vitorccs\LaravelCsv\Concerns\Importables\FromDisk;
use Vitorccs\LaravelCsv\Concerns\Importables\FromFile;
use Vitorccs\LaravelCsv\Concerns\Importables\FromResource;
use Vitorccs\LaravelCsv\Entities\CsvConfig;
use Vitorccs\LaravelCsv\Handlers\Readers\StreamHandler;

class ImportableService
{
    /**
     * @var CsvConfig
     */
    private CsvConfig $config;

    /**
     * @param CsvConfig $config
     */
    public function __construct(CsvConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param CsvConfig $config
     */
    public function setConfig(CsvConfig $config): void
    {
        $this->config = $config;
    }

    /**
     * @return CsvConfig
     */
    public function getConfig(): CsvConfig
    {
        return $this->config;
    }

    /**
     * @param object $importable
     * @return int
     */
    public function count(object $importable): int
    {
        return $this->getReader($importable)->count($importable);
    }

    /**
     * @param object $importable
     * @return array
     */
    public function getArray(object $importable): array
    {
        return $this->getReader($importable)->getRows($importable);
    }

    /**
     * @param object $importable
     * @param callable(array,int):void $callable
     * @param int|null $size
     * @return void
     */
    public function chunkArray(object   $importable,
                               callable $callable,
                               ?int     $size = null): void
    {
        $size = $size ?: $this->config->chunk_size;

        $this->getReader($importable)->chunkRows($importable, $callable, $size);
    }

    /**
     * @param object $importable
     * @return resource
     */
    protected function stream(object $importable)
    {
        if ($importable instanceof FromResource) {
            if (!is_resource($importable->resource())) {
                throw new \RuntimeException('Not a valid resource');
            }
            return $importable->resource();
        }

        if ($importable instanceof FromDisk) {
            $disk = $importable->disk() ?: $this->config->disk;
            return Storage::disk($disk)->readStream($importable->filename());
        }

        if ($importable instanceof FromContents) {
            $stream = fopen('php://temp', 'w+') or throw new \RuntimeException('Cannot open temp stream');
            fwrite($stream, $importable->contents());
            return $stream;
        }

        if ($importable instanceof FromFile) {
            $stream = fopen($importable->filename(), 'a+') or throw new \RuntimeException('Cannot file as stream');
            return $stream;
        }

        throw new \RuntimeException('Missing data source trait');
    }

    /**
     * @param object $importable
     * @return Reader
     */
    protected function getReader(object $importable): Reader
    {
        $resource = $this->stream($importable);

        /** @var Reader $reader */
        $reader = App::make(Reader::class, [
            'parser' => new ParserService($this->config),
            'handler' => new StreamHandler($this->config, $resource)
        ]);

        return $reader;
    }
}
