<?php

namespace Vitorccs\LaravelCsv\Tests\Concerns\Exportables;

use Vitorccs\LaravelCsv\Concerns\WithHeadings;
use Vitorccs\LaravelCsv\Tests\Data\Exports\NoHeadings\FromArrayExport as NoHeadingsExport;
use Vitorccs\LaravelCsv\Tests\Data\Exports\WithHeadings\FromArrayExport as WithHeadingsExport;
use Vitorccs\LaravelCsv\Tests\TestCase;

class FromArrayTest extends TestCase
{
    public function test_from_array()
    {
        $exports = [
            new NoHeadingsExport(),
            new WithHeadingsExport(),
        ];

        foreach ($exports as $export) {
            $export->store($this->filename);
            $actual = $this->getFromDisk($this->filename);

            $this->assertEquals($export->expected(), $actual);
        }
    }

    public function test_limit_from_array()
    {
        $limit = rand(1, 5);

        $exports = [
            new NoHeadingsExport($limit),
            new WithHeadingsExport($limit),
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
