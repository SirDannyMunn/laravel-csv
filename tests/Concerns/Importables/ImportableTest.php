<?php

namespace Vitorccs\LaravelCsv\Tests\Concerns\Importables;

use Vitorccs\LaravelCsv\Facades\CsvImporter;
use Vitorccs\LaravelCsv\Tests\Data\Imports\NoHeadings\FromContentsImport;
use Vitorccs\LaravelCsv\Tests\TestCase;

class ImportableTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->import = new FromContentsImport();
    }

    public function test_count()
    {
        $count = 100;

        CsvImporter::shouldReceive('count')
            ->once()
            ->with($this->import)
            ->andReturn($count);

        $this->assertEquals($count, $this->import->count());
    }

    public function test_get_array()
    {
        $array = [1, 2, 3];

        CsvImporter::shouldReceive('getArray')
            ->once()
            ->with($this->import)
            ->andReturn($array);

        $this->assertEquals($array, $this->import->getArray());
    }

    public function test_chunk_array()
    {
        $callable = function (array $rows) {
            $this->assertCount(count($this->import->getArray()), $rows);
        };
        $size = 100;

        CsvImporter::shouldReceive('chunkArray')
            ->once()
            ->with($this->import, $callable, $size);

        $this->import->chunkArray($callable, $size);
    }
}