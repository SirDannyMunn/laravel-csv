<?php

namespace Vitorccs\LaravelCsv\Handlers\Writers;

interface Handler
{
    /**
     * @return resource
     */
    public function getResource();

    /**
     * @param array $content
     * @return void
     */
    public function addContent(array $content): void;
}
