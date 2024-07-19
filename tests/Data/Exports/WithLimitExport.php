<?php

namespace Vitorccs\LaravelCsv\Tests\Data\Exports;

use Vitorccs\LaravelCsv\Concerns\Exportables\Exportable;
use Vitorccs\LaravelCsv\Concerns\Exportables\FromQuery;
use Vitorccs\LaravelCsv\Tests\Data\Stubs\TestCsv;

class WithLimitExport implements FromQuery
{
    use Exportable;

    public function limit(): ?int
    {
        return 10;
    }

    public function query()
    {
        return TestCsv::query();
    }
}
