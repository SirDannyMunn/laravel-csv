<?php

namespace Vitorccs\LaravelCsv\Tests\Concerns;

use Carbon\Carbon;
use Vitorccs\LaravelCsv\Tests\Data\Exports\WithColumnFormattingExport;
use Vitorccs\LaravelCsv\Tests\Data\Imports\WithColumnFormattingImport;
use Vitorccs\LaravelCsv\Tests\TestCase;

class WithColumnFormattingTest extends TestCase
{
    public function test_export_with_column_formatting()
    {
        $export = new WithColumnFormattingExport();

        $config = $export->getConfig();
        $config->format_date = $export->formatDate();
        $config->format_datetime = $export->formatDateTime();
        $config->format_number_thousand_sep = $export->thousandSeparator();
        $config->format_number_decimal_sep = $export->decimalSeparator();
        $export->setConfig($config);

        $export->store($this->filename);
        $actual = $this->getFromDisk($this->filename);

        $this->assertEquals($export->expected(), $actual);
    }

    public function test_import_with_column_formatting()
    {
        $import = new WithColumnFormattingImport();

        $config = $import->getConfig();
        $config->format_date = $import->formatDate();
        $config->format_datetime = $import->formatDateTime();
        $config->format_number_thousand_sep = $import->thousandSeparator();
        $config->format_number_decimal_sep = $import->decimalSeparator();
        $import->setConfig($config);

        $expected = $import->expected();
        $actualRows = $import->getArray();

        foreach ($actualRows as $i => $actualRow) {
            $this->assertEquals($expected[$i][0], $actualRow[0]);
            $this->assertEquals($expected[$i][1], $actualRow[1]);
            if ($i == 0) {
                $this->assertInstanceOf(Carbon::class, $actualRow[2]);
                $this->assertEquals($expected[$i][2]->toDateString(), $actualRow[2]->toDateString());
                $this->assertInstanceOf(Carbon::class, $actualRow[3]);
                $this->assertEquals($expected[$i][3]->toDateTimeString(), $actualRow[3]->toDateTimeString());
            } else {
                $this->assertSame($expected[$i][2], $actualRow[2]);
                $this->assertSame($expected[$i][3], $actualRow[3]);
            }
        }
    }
}
