<?php

namespace Vitorccs\LaravelCsv\Tests\Data\Exports\WithHeadings;

use Vitorccs\LaravelCsv\Concerns\Exportables\Exportable;
use Vitorccs\LaravelCsv\Concerns\Exportables\FromArray;
use Vitorccs\LaravelCsv\Concerns\WithHeadings;

class FromArrayExport implements FromArray, WithHeadings
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

    public function array(): array
    {
        return $this->contents();
    }
}
