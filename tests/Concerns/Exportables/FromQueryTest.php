<?php

namespace Vitorccs\LaravelCsv\Tests\Concerns\Exportables;

use Vitorccs\LaravelCsv\Concerns\WithHeadings;
use Vitorccs\LaravelCsv\Tests\Data\Database\Seeders\TestCsvSeeder;
use Vitorccs\LaravelCsv\Tests\Data\Exports\NoHeadings\FromEloquentBuilderExport as EloquentNoHeadingsExport;
use Vitorccs\LaravelCsv\Tests\Data\Exports\NoHeadings\FromQueryBuilderExport as QueryNoHeadingsExport;
use Vitorccs\LaravelCsv\Tests\Data\Exports\WithHeadings\FromEloquentBuilderExport as EloquentWithHeadingsExport;
use Vitorccs\LaravelCsv\Tests\Data\Exports\WithHeadings\FromQueryBuilderExport as QueryWithHeadingsExport;
use Vitorccs\LaravelCsv\Tests\TestCase;

class FromQueryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(TestCsvSeeder::class);
    }

    public function test_from_builder()
    {
        $exports = [
            new EloquentNoHeadingsExport(),
            new EloquentWithHeadingsExport(),
            new QueryNoHeadingsExport(),
            new QueryWithHeadingsExport(),
        ];

        foreach ($exports as $export) {
            $export->store($this->filename);
            $actual = $this->getFromDisk($this->filename);

            $this->assertEquals($export->expected(), $actual);
        }
    }


    public function test_limit_from_builder()
    {
        $limit = rand(1, 5);

        $exports = [
            new EloquentNoHeadingsExport($limit),
            new EloquentWithHeadingsExport($limit),
            new QueryNoHeadingsExport($limit),
            new QueryWithHeadingsExport($limit),
        ];

        foreach ($exports as $export) {
            $export->store($this->filename);
            $actual = $this->getFromDiskArray($this->filename);
            $expected = $export instanceof WithHeadings
                ? $limit + 1
                : $limit;

            $this->assertCount($expected, $actual);
        }
    }
}
