<?php

namespace Vitorccs\LaravelCsv\Tests\Data\Stubs;

use Illuminate\Database\Eloquent\Model;

class TestCsv extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id',
        'integer',
        'decimal',
        'string',
        'timestamp',
        'boolean'
    ];
}
