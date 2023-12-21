<?php

namespace Neuedev\Apineu\Action;

use Neuedev\Apineu\Api\ToSchemaJsonInterface;
use Neuedev\Apineu\DI\ContainerAwareInterface;

class ActionInput extends ActionResponse implements ToSchemaJsonInterface, ContainerAwareInterface
{
    protected function getNameForException(): string
    {
        return 'input';
    }
}
