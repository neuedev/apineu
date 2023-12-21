<?php

namespace Neuedev\Apineu\Bag;

use Neuedev\Apineu\Api\ToSchemaJsonTrait;
use Neuedev\Apineu\DI\ContainerAwareTrait;

class BagEntry implements BagEntryInterface
{
    use ContainerAwareTrait;
    use ToSchemaJsonTrait;
}
