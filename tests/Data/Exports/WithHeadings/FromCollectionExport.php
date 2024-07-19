<?php

namespace Vitorccs\LaravelCsv\Tests\Data\Exports\WithHeadings;

use Illuminate\Support\Collection;
use Vitorccs\LaravelCsv\Concerns\Exportables\Exportable;
use Vitorccs\LaravelCsv\Concerns\Exportables\FromCollection;
use Vitorccs\LaravelCsv\Concerns\WithHeadings;

class FromCollectionExport implements FromCollection, WithHeadings
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
