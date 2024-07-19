<?php

namespace Vitorccs\LaravelCsv\Tests\Data\Imports\WithHeadings;

trait FromImportTrait
{
    public function headings(): array
    {
        return ['H1', 'H2', 'H3'];
    }

    public function contents(): string
    {
        return "H,H,H\n\"a 1\",\"b 1\",\"c 1\"\na2,b2,c2\na3,b3,c3\na4,b4,c4\na5,b5,c5\na6,b6,c6\na7,b7,c7\na8,b8,c8\na9,b9,c9\na10,b10,c10";
    }

    public function expected(): array
    {
        return [
            ['H1', 'H2', 'H3'],
            ['a 1', 'b 1', 'c 1'],
            ['a2', 'b2', 'c2'],
            ['a3', 'b3', 'c3'],
            ['a4', 'b4', 'c4'],
            ['a5', 'b5', 'c5'],
            ['a6', 'b6', 'c6'],
            ['a7', 'b7', 'c7'],
            ['a8', 'b8', 'c8'],
            ['a9', 'b9', 'c9'],
            ['a10', 'b10', 'c10']
        ];
    }
}