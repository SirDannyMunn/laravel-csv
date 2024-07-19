<?php

namespace Vitorccs\LaravelCsv\Tests\Data\Exports\NoHeadings;

use Illuminate\Support\Facades\DB;
use Vitorccs\LaravelCsv\Concerns\Exportables\Exportable;
use Vitorccs\LaravelCsv\Concerns\Exportables\FromQuery;

class FromQueryBuilderExport implements FromQuery
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
