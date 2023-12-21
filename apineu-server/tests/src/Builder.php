<?php

namespace Neuedev\Apineu\Test;

use Neuedev\Apineu\DI\Container;

class Builder
{
    protected Container $container;

    public function __construct(?Container $container = null)
    {
        $this->container = $container ?? new Container();
    }
}
