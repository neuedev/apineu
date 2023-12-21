<?php

namespace Neuedev\Apineu\Api;

use Neuedev\Apineu\Exception\Exception;

class NotFoundException extends Exception
{
    public $statusCode = 404;
}
