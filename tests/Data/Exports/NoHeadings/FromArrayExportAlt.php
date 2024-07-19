<?php

namespace Vitorccs\LaravelCsv\Tests\Data\Exports\NoHeadings;

use Vitorccs\LaravelCsv\Concerns\Exportables\Exportable;
use Vitorccs\LaravelCsv\Concerns\Exportables\FromArray;

class FromArrayExportAlt implements FromArray
{
    use Exportable;

    private ?int $limit;

    public function __construct(?int $limit = null)
    {
        $this->limit = $limit;
    }

    public function limit(): ?int
    {
        return $this->limit;
    }

    public function expected(): string
    {
        return "'a 1'|'b 1'|'c 1'\n'a 2'|'b 2'|'c 2'";
    }

    public function csvDelimiter(): string
    {
        return '|';
    }

    public function csvEnclosure(): string
    {
        return "'";
    }

    public function array(): array
    {
        return [
            ['a 1', 'b 1', 'c 1'],
            ['a 2', 'b 2', 'c 2'],
        ];
    }
}
