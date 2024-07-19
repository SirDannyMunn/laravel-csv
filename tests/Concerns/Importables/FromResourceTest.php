<?php

namespace Vitorccs\LaravelCsv\Tests\Concerns\Importables;

use Vitorccs\LaravelCsv\Concerns\WithHeadings;
use Vitorccs\LaravelCsv\Tests\Data\Imports\NoHeadings\FromResourceImport as NoHeadingsImport;
use Vitorccs\LaravelCsv\Tests\Data\Imports\WithHeadings\FromResourceImport as WithHeadingsImport;

class FromResourceTest extends AbstractTestCase
{
    /**
     * @dataProvider diskDataProvider
     */
    public function test_all_from_resource(string $source)
    {
        $imports = [
            new NoHeadingsImport($source),
            new WithHeadingsImport($source),
        ];

        foreach ($imports as $import) {
            $actual = $import->getArray();

            $this->assertEquals($actual, $import->expected());
        }
    }

    /**
     * @dataProvider diskDataProvider
     */
    public function test_limit_from_resource(string $source)
    {
        $limit = rand(1, 9);

        $imports = [
            new NoHeadingsImport($source, $limit),
            new WithHeadingsImport($source, $limit),
        ];

        foreach ($imports as $import) {
            $actual = $import->getArray();
            $expected = $import instanceof WithHeadings
                ? $limit + 1
                : $limit;

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
            ? new WithHeadingsImport(limit: $limit)
            : new NoHeadingsImport(limit: $limit);

        $this->assertChunk($import, $size, $expectedCalls, $expectedRecords);
    }

    public static function diskDataProvider(): array
    {
        return [
            'from temp' => [
                'php://temp',
            ],
            'from memory' => [
                'php://memory',
            ]
        ];
    }
}