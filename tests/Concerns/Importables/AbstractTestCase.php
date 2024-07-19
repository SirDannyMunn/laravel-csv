<?php

namespace Vitorccs\LaravelCsv\Tests\Concerns\Importables;

use Vitorccs\LaravelCsv\Tests\Data\Imports\NoHeadings\FromImportTrait as NoHeadingsTrait;
use Vitorccs\LaravelCsv\Tests\Data\Imports\WithHeadings\FromImportTrait as WithHeadingsTrait;
use Vitorccs\LaravelCsv\Tests\TestCase;

abstract class AbstractTestCase extends TestCase
{
    protected function assertChunk(object $import,
                                   int    $size,
                                   int    $expectedCalls,
                                   array  $expectedRecords): void
    {
        $actualCalls = 0;

        $callable = function (array $rows, int $chunk) use ($import, $expectedRecords, $size, &$actualCalls) {
            $offset = $size * $actualCalls;
            $expectedRows = array_slice($expectedRecords, $offset, $size);

            $this->assertSame($actualCalls, $chunk);
            $this->assertCount(count($expectedRows), $rows);
            $this->assertSame($expectedRows, $rows);

            $actualCalls++;
        };

        $import->chunkArray($callable, $size);

        if (method_exists($import, 'delete')) {
            $import->delete();
        };

        $this->assertEquals($expectedCalls, $actualCalls);
    }

    public static function noHeadingsChunkProvider(): array
    {
        $import = new class() {
            use NoHeadingsTrait;
        };

        return [
            'no limit' => [
                null,
                2,
                5,
                $import->expected(),
                false
            ],
            'multiple of chunk size' => [
                6,
                2,
                3,
                array_slice($import->expected(), 0, 6),
                false
            ],
            'less than chunk size' => [
                5,
                2,
                3,
                array_slice($import->expected(), 0, 5),
                false
            ],
            'same of results quantity' => [
                10,
                2,
                5,
                $import->expected(),
                false
            ],
            'greater than results quantity' => [
                100,
                2,
                5,
                $import->expected(),
                false
            ],
        ];
    }

    public static function withHeadingChunkProvider(): array
    {
        $import = new class() {
            use WithHeadingsTrait;
        };

        return [
            'no limit' => [
                null,
                2,
                6,
                $import->expected(),
                true
            ],
            'multiple of chunk size' => [
                6,
                2,
                4,
                array_slice($import->expected(), 0, 7),
                true
            ],
            'less than chunk size' => [
                5,
                2,
                3,
                array_slice($import->expected(), 0, 6),
                true
            ],
            'same of results quantity' => [
                10,
                2,
                6,
                $import->expected(),
                true
            ],
            'greater than results quantity' => [
                100,
                2,
                6,
                $import->expected(),
                true
            ],
        ];
    }
}