<?php
/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Vitorccs\LaravelCsv\Tests\Data\Stubs\TestCsv;

$factory->define(TestCsv::class, function (Faker $faker) {
    return [
        'integer' => $faker->numberBetween(-1000, 1000),
        'decimal' => $faker->randomFloat(),
        'string' => $faker->word(),
        'timestamp' => $faker->dateTime()
    ];
});
