<?php

namespace Vitorccs\LaravelCsv\Concerns\Importables;

interface FromDisk
{
    /**
     * @return string|null
     */
    public function disk(): ?string;

    /**
     * @return string
     */
    public function filename(): string;
}
