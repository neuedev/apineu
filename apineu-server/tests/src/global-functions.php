<?php

use function Neuedev\Apineu\Test\toArray as testToArray;

function toArray(mixed $value, bool $onlyVisible = true): mixed
{
    return testToArray($value, $onlyVisible);
}
