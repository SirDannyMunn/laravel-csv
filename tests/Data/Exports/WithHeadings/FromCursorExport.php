<?php

namespace Vitorccs\LaravelCsv\Tests\Data\Exports\WithHeadings;

use Illuminate\Support\LazyCollection;
use Vitorccs\LaravelCsv\Concerns\Exportables\Exportable;
use Vitorccs\LaravelCsv\Concerns\Exportables\FromCollection;
use Vitorccs\LaravelCsv\Concerns\WithHeadings;
use Vitorccs\LaravelCsv\Tests\Data\Stubs\TestCsv;

class FromCursorExport implements FromCollection, WithHeadings
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

    public function collection(): LazyCollection
    {
        return TestCsv::cursor();
    }
}