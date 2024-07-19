<?php

namespace Vitorccs\LaravelCsv\Tests\Data\Exports\NoHeadings;

use Illuminate\Support\Collection;
use Vitorccs\LaravelCsv\Concerns\Exportables\Exportable;
use Vitorccs\LaravelCsv\Concerns\Exportables\FromCollection;

class FromCollectionExport implements FromCollection
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

    public function collection(): Collection
    {
        return collect($this->contents());
    }
}
