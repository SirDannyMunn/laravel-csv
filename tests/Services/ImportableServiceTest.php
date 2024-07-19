<?php

namespace Vitorccs\LaravelCsv\Tests\Services;

use Vitorccs\LaravelCsv\Concerns\WithHeadings;
use Vitorccs\LaravelCsv\Entities\CsvConfig;
use Vitorccs\LaravelCsv\Services\ImportableService;
use Vitorccs\LaravelCsv\Tests\Data\Database\Seeders\TestCsvSeeder;
use Vitorccs\LaravelCsv\Tests\Data\Imports\NoHeadings\FromContentsImport;
use Vitorccs\LaravelCsv\Tests\Data\Imports\NoHeadings\FromContentsImportAlt;
use Vitorccs\LaravelCsv\Tests\Data\Imports\NoHeadings\FromDiskImport;
use Vitorccs\LaravelCsv\Tests\Data\Imports\NoHeadings\FromFileImport;
use Vitorccs\LaravelCsv\Tests\Data\Imports\NoHeadings\FromResourceImport;
use Vitorccs\LaravelCsv\Tests\Data\Imports\WithHeadingsImport;
use Vitorccs\LaravelCsv\Tests\TestCase;

class ImportableServiceTest extends TestCase
{
    protected ImportableService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(TestCsvSeeder::class);

        $this->service = app(ImportableService::class);
    }

    public function test_count()
    {
        $imports = [
            new FromContentsImport(),
            new FromDiskImport(),
            new FromFileImport(),
            new FromResourceImport(),
            new WithHeadingsImport()
        ];

        foreach ($imports as $import) {
            $actual = $this->service->count($import);
            $expected = count($import->expected());

            if (method_exists($import, 'delete')) {
                $import->delete();
            }

            if ($import instanceof WithHeadings) {
                $expected--;
            }

            $this->assertSame($expected, $actual);
        }
    }

    public function test_from_disk()
    {
        $import = new FromDiskImport();

        $actual = $this->service->getArray($import);
        $import->delete();

        $this->assertSame($import->expected(), $actual);
    }

    public function test_set_config()
    {
        $import = new FromContentsImportAlt();

        $csvConfig = new CsvConfig();
        $csvConfig->csv_delimiter = $import->csvDelimiter();
        $csvConfig->csv_enclosure = $import->csvEnclosure();
        $this->service->setConfig($csvConfig);

        $actual = $this->service->getArray($import);

        $this->assertEquals($import->expected(), $actual);
    }
}
