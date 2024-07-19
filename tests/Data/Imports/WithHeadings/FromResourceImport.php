<?php

namespace Vitorccs\LaravelCsv\Tests\Data\Imports\WithHeadings;

use Vitorccs\LaravelCsv\Concerns\Importables\FromResource;
use Vitorccs\LaravelCsv\Concerns\Importables\Importable;
use Vitorccs\LaravelCsv\Concerns\WithHeadings;

class FromResourceImport implements FromResource, WithHeadings
{
    use Importable, FromImportTrait;

    /**
     * @var resource
     */
    protected $resource;

    public function __construct(string       $source = 'php://temp',
                                private ?int $limit = null)
    {
        $this->resource = fopen($source, 'w+') or throw new \RuntimeException('Fail to create resource');
        fputs($this->resource, $this->contents());
    }

    public function limit(): ?int
    {
        return $this->limit;
    }

    /**
     * @return resource
     */
    public function resource()
    {
        return $this->resource;
    }
}