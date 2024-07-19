<?php

namespace Vitorccs\LaravelCsv\Tests\Data\Exports\NoHeadings;

use Vitorccs\LaravelCsv\Tests\Data\Database\Seeders\TestCsvSeeder;

trait FromExportTrait
{
    public function contents(): array
    {
        return array_map(fn(array $user) => array_values($user), TestCsvSeeder::USERS);
    }

    public function expected(): string
    {
        return '1,1,1.23,text_1,2025-01-01' . "\n" .
            '2,-1,-1.23,text_2,2025-01-02' . "\n" .
            '3,1000,1000.23,text_3,2025-01-03' . "\n" .
            '4,-1000,-1000.23,text_4,2025-01-04' . "\n" .
            '5,1000000,1000000.23,text_5,2025-01-05' . "\n" .
            '6,-1000000,-1000000.23,text_6,2025-01-06';
    }
}