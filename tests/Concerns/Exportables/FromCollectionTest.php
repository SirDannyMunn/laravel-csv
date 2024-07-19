<?php

namespace Vitorccs\LaravelCsv\Tests\Concerns\Exportables;

use Vitorccs\LaravelCsv\Concerns\WithHeadings;
use Vitorccs\LaravelCsv\Tests\Data\Database\Seeders\TestCsvSeeder;
use Vitorccs\LaravelCsv\Tests\Data\Exports\NoHeadings\FromCollectionExport as CollectionNoHeadingsExport;
use Vitorccs\LaravelCsv\Tests\Data\Exports\NoHeadings\FromCursorExport as CursorNoHeadingsExport;
use Vitorccs\LaravelCsv\Tests\Data\Exports\WithHeadings\FromCollectionExport as CollectionWithHeadingsExport;
use Vitorccs\LaravelCsv\Tests\Data\Exports\WithHeadings\FromCursorExport as CursorWithHeadingsExport;
use Vitorccs\LaravelCsv\Tests\TestCase;

class FromCollectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(TestCsvSeeder::class);
    }

    public function test_from_collection()
    {
        $exports = [
            new CollectionNoHeadingsExport(),
            new CollectionWithHeadingsExport(),
            new CursorNoHeadingsExport(),
            new CursorWithHeadingsExport(),
        ];

        foreach ($exports as $export) {
            $export->store($this->filename);
            $actual = $this->getFromDisk($this->filename);

            $this->assertEquals($export->expected(), $actual);
        }
    }

    public function test_limit_from_collection()
    {
        $limit = rand(1, 5);

        $exports = [
            new CollectionNoHeadingsExport($limit),
            new CollectionWithHeadingsExport($limit),
            new CursorNoHeadingsExport($limit),
            new CursorWithHeadingsExport($limit),
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
