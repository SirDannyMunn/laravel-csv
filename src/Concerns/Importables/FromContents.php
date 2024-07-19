<?php

namespace Vitorccs\LaravelCsv\Concerns\Importables;

interface FromContents
{
    /**
     * @return string
     */
    public function contents(): string;
}
