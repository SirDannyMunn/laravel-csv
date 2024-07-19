<?php

namespace Vitorccs\LaravelCsv\Tests\Concerns\Importables;

use Vitorccs\LaravelCsv\Concerns\WithHeadings;
use Vitorccs\LaravelCsv\Tests\Data\Imports\NoHeadings\FromFileImport as NoHeadingsImport;
use Vitorccs\LaravelCsv\Tests\Data\Imports\WithHeadings\FromFileImport as WithHeadingsImport;

class FromFileTest extends AbstractTestCase
{
    public function test_all_from_file()
    {
        $imports = [
            new NoHeadingsImport(),
            new WithHeadingsImport(),
        ];

        foreach ($imports as $import) {
            $actual = $import->getArray();
            $import->delete();

            $this->assertEquals($actual, $import->expected());
        }
    }

    public function test_limit_from_file()
    {
        $limit = rand(1, 9);

        $imports = [
            new NoHeadingsImport($limit),
            new WithHeadingsImport($limit),
        ];

        foreach ($imports as $import) {
            $actual = $import->getArray();
            $expected = $import instanceof WithHeadings
                ? $limit + 1
                : $limit;
            $import->delete();

            $this->assertCount($expected, $actual);
        }
    }

    /**
     * @dataProvider noHeadingsChunkProvider
     * @dataProvider withHeadingChunkProvider
     */
    public function test_chunk_from_file(?int  $limit,
                                         int   $size,
                                         int   $expectedCalls,
                                         array $expectedRecords,
                                         bool  $withHeadings)
    {
        $import = $withHeadings
            ? new WithHeadingsImport($limit)
            : new NoHeadingsImport($limit);

        $this->assertChunk($import, $size, $expectedCalls, $expectedRecords);
    }
}