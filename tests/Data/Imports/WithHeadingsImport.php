<?php

namespace Vitorccs\LaravelCsv\Tests\Data\Imports;

use Vitorccs\LaravelCsv\Concerns\Importables\FromContents;
use Vitorccs\LaravelCsv\Concerns\Importables\Importable;
use Vitorccs\LaravelCsv\Concerns\WithHeadings;

class WithHeadingsImport implements WithHeadings, FromContents
{
    use Importable;

    public function contents(): string
    {
        return "column1,column2,column3\na1,b1,c1\na2,b2,c2";
    }

    public function headings(): array
    {
        return [
            'A',
            'B',
            'C'
        ];
    }

    public function expected(): array
    {
        return [
            ['A', 'B', 'C'],
            ['a1', 'b1', 'c1'],
            ['a2', 'b2', 'c2'],
        ];
    }
}
