<?php

namespace Vitorccs\LaravelCsv\Concerns\Importables;

interface FromFile
{
    /**
     * @return string
     */
    public function filename(): string;
}
