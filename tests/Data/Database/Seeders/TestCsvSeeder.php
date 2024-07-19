<?php

namespace Vitorccs\LaravelCsv\Tests\Data\Database\Seeders;

use Illuminate\Database\Seeder;
use Vitorccs\LaravelCsv\Tests\Data\Stubs\TestCsv;

class TestCsvSeeder extends Seeder
{
    const USERS = [
        [
            'id' => 1,
            'integer' => 1,
            'decimal' => 1.23,
            'string' => 'text_1',
            'timestamp' => '2025-01-01'
        ],
        [
            'id' => 2,
            'integer' => -1,
            'decimal' => -1.23,
            'string' => 'text_2',
            'timestamp' => '2025-01-02'
        ],
        [
            'id' => 3,
            'integer' => 1000,
            'decimal' => 1000.23,
            'string' => 'text_3',
            'timestamp' => '2025-01-03'
        ],
        [
            'id' => 4,
            'integer' => -1000,
            'decimal' => -1000.23,
            'string' => 'text_4',
            'timestamp' => '2025-01-04'
        ],
        [
            'id' => 5,
            'integer' => 1000000,
            'decimal' => 1000000.23,
            'string' => 'text_5',
            'timestamp' => '2025-01-05'
        ],
        [
            'id' => 6,
            'integer' => -1000000,
            'decimal' => -1000000.23,
            'string' => 'text_6',
            'timestamp' => '2025-01-06'
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::USERS as $user) {
            TestCsv::create($user);
        }
    }
}
