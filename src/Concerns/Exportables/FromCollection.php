<?php

namespace Vitorccs\LaravelCsv\Concerns\Exportables;

use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

interface FromCollection
{
    /**
     * @return Collection|LazyCollection
     */
    public function collection();
}
