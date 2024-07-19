<?php

namespace Vitorccs\LaravelCsv\Services;

use Vitorccs\LaravelCsv\Concerns\WithColumnFormatting;
use Vitorccs\LaravelCsv\Concerns\WithHeadings;
use Vitorccs\LaravelCsv\Concerns\WithMapping;
use Vitorccs\LaravelCsv\Enum\CellFormat;
use Vitorccs\LaravelCsv\Handlers\Readers\Handler;
use Vitorccs\LaravelCsv\Helpers\CsvHelper;

class Reader
{
    /**
     * @var ParserService
     */
    protected ParserService $parser;

    /**
     * @var Handler
     */
    protected Handler $handler;

    /**
     * @param Handler $handler
     * @param ParserService $parser
     */
    public function __construct(ParserService $parser,
                                Handler       $handler)
    {
        $this->parser = $parser;
        $this->handler = $handler;
    }

    /**
     * @param object $importable
     * @return int
     */
    public function count(object $importable): int
    {
        $offset = $importable instanceof WithHeadings ? 1 : 0;
        $count = $this->handler->count();

        return $count - $offset;
    }

    /**
     * @param object $importable
     * @param callable(array,int):void $callable
     * @param int $size
     * @return void
     */
    public function chunkRows(object   $importable,
                              callable $callable,
                              int      $size): void
    {
        $counter = 0;
        $hasHeadings = $importable instanceof WithHeadings;
        $maxRows = $this->getMaxRows($importable, $hasHeadings);

        $wrapper = function (array $rows) use ($callable, $hasHeadings, $importable, &$counter) {
            $hasHeadings = $counter === 0 && $hasHeadings;
            $rows = $this->prepareRows($importable, $rows, $hasHeadings);
            $callable($rows, $counter);
            $counter++;
        };

        $this->handler->getChunk($wrapper, $size, $maxRows);
    }

    /**
     * @param object $importable
     * @return array
     */
    public function getRows(object $importable): array
    {
        $hasHeadings = $importable instanceof WithHeadings;
        $maxRows = $this->getMaxRows($importable, $hasHeadings);
        $rows = $this->handler->getAll($maxRows);

        return $this->prepareRows($importable, $rows, $hasHeadings);
    }

    /**
     * @param object $importable
     * @param array $rows
     * @param bool $hasHeadings
     * @return array
     */
    protected function prepareRows(object $importable,
                                   array  $rows,
                                   bool   $hasHeadings): array
    {
        $formats = $importable instanceof WithColumnFormatting ? $importable->columnFormats() : [];
        $withMapping = $importable instanceof WithMapping;

        foreach ($rows as $index => $row) {
            if ($index === 0 && $hasHeadings) {
                $formattedRow = $importable->headings();
            } else {
                $mappedRow = $withMapping ? $importable->map($row) : $row;
                $formattedRow = $this->applyFormatting($mappedRow, $formats);
            }

            $rows[$index] = $formattedRow;
        }

        return $rows;
    }

    /**
     * @param object $importable
     * @param bool $hasHeadings
     * @return int|null
     */
    protected function getMaxRows(object $importable, bool $hasHeadings): ?int
    {
        return $importable->limit()
            ? $importable->limit() + intval($hasHeadings)
            : null;
    }

    /**
     * @param array $row
     * @param array $formats
     * @return array
     */
    protected function applyFormatting(array $row,
                                       array $formats): array
    {
        return array_map(
            fn($value, int $columnIndex) => $this->formatCellValue($value, $formats, $columnIndex),
            $row,
            array_keys($row)
        );
    }

    /**
     * @param mixed $value
     * @param array $formats
     * @param int $columnIndex
     * @return mixed
     */
    protected function formatCellValue(mixed $value,
                                       array $formats,
                                       int   $columnIndex): mixed
    {
        $columnLetter = CsvHelper::getColumnLetter($columnIndex + 1);
        $format = $formats[$columnLetter] ?? null;

        if (!strlen(trim($value))) return $value;

        if ($format === CellFormat::DATE) {
            return $this->parser->toCarbonDate($value) ?: $value;
        }

        if ($format === CellFormat::DATETIME) {
            return $this->parser->toCarbonDatetime($value) ?: $value;
        }

        if ($format === CellFormat::DECIMAL) {
            return $this->parser->toFloat($value) ?: $value;
        }

        if ($format === CellFormat::INTEGER) {
            return $this->parser->toInteger($value) ?: $value;
        }

        return $value;
    }
}
