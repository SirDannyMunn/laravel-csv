<?php

namespace Vitorccs\LaravelCsv\Tests\Data\Exports;

use Vitorccs\LaravelCsv\Concerns\Exportables\Exportable;
use Vitorccs\LaravelCsv\Concerns\Exportables\FromQuery;
use Vitorccs\LaravelCsv\Concerns\WithMapping;
use Vitorccs\LaravelCsv\Tests\Data\Stubs\TestCsv;

class WithMappingExport implements FromQuery, WithMapping
{
    use Exportable;

    public function query()
    {
        return TestCsv::query();
    }

    public function map($row): array
    {
        return [
            'replace',
            'concatenate_' . $row->string,
            $row->integer + 1
        ];
    }

    public function expected(): string
    {
        return 'replace,concatenate_text_1,2' . "\n" .
            'replace,concatenate_text_2,0' . "\n" .
            'replace,concatenate_text_3,1001' . "\n" .
            'replace,concatenate_text_4,-999' . "\n" .
            'replace,concatenate_text_5,1000001' . "\n" .
            'replace,concatenate_text_6,-999999';
    }
}
