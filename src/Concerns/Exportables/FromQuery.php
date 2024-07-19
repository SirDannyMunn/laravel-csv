<?php

namespace Vitorccs\LaravelCsv\Concerns\Exportables;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;

interface FromQuery
{
    /**
     * @return Builder|EloquentBuilder
     */
    public function query();
}
