<?php

namespace Neuedev\Apineu\Filter\Filters;

use Neuedev\Apineu\Filter\Filter;

class PageFilter extends Filter
{
    protected static string $type = 'Afeefa.PageFilter';

    protected function setup(): void
    {
        $this->default(1);
    }
}
