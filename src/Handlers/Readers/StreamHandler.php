<?php

namespace Vitorccs\LaravelCsv\Handlers\Readers;

use Vitorccs\LaravelCsv\Entities\CsvConfig;
use Vitorccs\LaravelCsv\Helpers\CsvHelper;

class StreamHandler implements Handler
{
    /**
     * @return resource
     */
    protected $stream;

    /**
     * @var CsvConfig
     */
    private CsvConfig $csvConfig;

    /**
     * @param CsvConfig $csvConfig
     * @param resource $resource
     */
    public function __construct(CsvConfig $csvConfig, $resource)
    {
        $this->stream = $resource;
        $this->csvConfig = $csvConfig;
    }

    /**
     * @return resource
     */
    public function getResource()
    {
        return $this->stream;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        $i = 0;
        rewind($this->stream);

        while (!feof($this->stream)) {
            $row = fgets($this->stream);
            if (empty($row)) continue;
            $i++;
        }

        return $i;
    }

    /**
     * @param callable(array,int):void $callable
     * @param int $size
     * @param int|null $maxRecords
     * @return void
     */
    public function getChunk(callable $callable,
                             int      $size,
                             ?int     $maxRecords = null): void
    {
        $this->prepareForReading();
        $counter = 0;
        $isMaxRecords = false;

        while (!feof($this->stream) && !$isMaxRecords) {
            $remaining = $maxRecords
                ? min($maxRecords - $counter, $size)
                : $size;
            $rows = $this->readStream($remaining);
            $callable($rows);
            $counter += count($rows);
            $isMaxRecords = $maxRecords && $counter >= $maxRecords;
        }
    }

    /**
     * @param int|null $maxRecords
     * @return array
     */
    public function getAll(?int $maxRecords = null): array
    {
        $this->prepareForReading();

        return $this->readStream($maxRecords);
    }

    private function readStream(?int $quantity = null): array
    {
        $rows = [];
        $counter = 0;
        $isMaxQuantity = false;

        while (!feof($this->stream) && !$isMaxQuantity) {
            $row = fgetcsv(
                $this->stream,
                null,
                $this->csvConfig->csv_delimiter,
                $this->csvConfig->csv_enclosure,
                $this->csvConfig->csv_escape
            );
            if (!is_array($row)) continue;
            $rows[] = $row;
            $counter++;
            $isMaxQuantity = $quantity && $counter >= $quantity;
        }

        return $rows;
    }

    /**
     * @return void
     */
    private function prepareForReading(): void
    {
        rewind($this->stream);

        // remove UTF-8 BOM character
        if (fgets($this->stream, 4) !== CsvHelper::getBom()) {
            rewind($this->stream);
        }
    }
}
