<?php

namespace Vitorccs\LaravelCsv\Tests\Data\Exports;

use Vitorccs\LaravelCsv\Concerns\Exportables\Exportable;
use Vitorccs\LaravelCsv\Concerns\Exportables\FromArray;
use Vitorccs\LaravelCsv\Concerns\WithHeadings;

class WithHeadingsExport implements WithHeadings, FromArray
{
    use Exportable;

    public function array(): array
    {
        return [
            ['a1', 'b1', 'c1'],
            ['a2', 'b2', 'c2'],
        ];
    }

    public function headings(): array
    {
        return [
            'A',
            'B',
            'C'
        ];
    }

    public function expected(): string
    {
        return "A,B,C\na1,b1,c1\na2,b2,c2";
    }
}
