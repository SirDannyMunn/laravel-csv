<?php

namespace Vitorccs\LaravelCsv\Tests\Data\Exports;

use Vitorccs\LaravelCsv\Concerns\Exportables\Exportable;
use Vitorccs\LaravelCsv\Concerns\Exportables\FromQuery;
use Vitorccs\LaravelCsv\Concerns\WithMapping;
use Vitorccs\LaravelCsv\Tests\Data\Stubs\TestCsv;

class WithMappingExportSimple implements FromQuery, WithMapping
{
    use Exportable;

    public function query()
    {
        return TestCsv::query();
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
        ];
    }
}
