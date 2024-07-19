<?php

namespace Vitorccs\LaravelCsv\Handlers\Writers;

class ArrayHandler implements Handler
{
    /**
     * @var array
     */
    protected array $handler = [];

    /**
     * @return array
     */
    public function getResource(): array
    {
        return $this->handler;
    }

    /**
     * @param array $content
     * @return void
     */
    public function addContent(array $content): void
    {
        $this->handler[] = $content;
    }
}
