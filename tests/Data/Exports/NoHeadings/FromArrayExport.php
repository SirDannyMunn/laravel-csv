<?php

namespace Vitorccs\LaravelCsv\Tests\Data\Exports\NoHeadings;

use Vitorccs\LaravelCsv\Concerns\Exportables\Exportable;
use Vitorccs\LaravelCsv\Concerns\Exportables\FromArray;

class FromArrayExport implements FromArray
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
