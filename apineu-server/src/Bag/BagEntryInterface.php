<?php

namespace Neuedev\Apineu\Bag;

use Neuedev\Apineu\Api\ToSchemaJsonInterface;
use Neuedev\Apineu\DI\ContainerAwareInterface;

interface BagEntryInterface extends ToSchemaJsonInterface, ContainerAwareInterface
{
}
