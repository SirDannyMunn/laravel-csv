<?php

namespace Vitorccs\LaravelCsv\Tests\Data\Exports\WithHeadings;

use Illuminate\Support\Facades\DB;
use Vitorccs\LaravelCsv\Concerns\Exportables\Exportable;
use Vitorccs\LaravelCsv\Concerns\Exportables\FromQuery;
use Vitorccs\LaravelCsv\Concerns\WithHeadings;

class FromQueryBuilderExport implements FromQuery, WithHeadings
{
    use Exportable, FromExportTrait;

    private ?int $limit;

    public function __construct(?int $limit = null)
    {
        $this->limit = $limit;
    }

    public function limit(): ?int
    {
        return $this->limit;
    }

    public function query()
    {
        return DB::table('test_csvs');
    }
}
