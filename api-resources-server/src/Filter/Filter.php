<?php

namespace Afeefa\ApiResources\Filter;

use Afeefa\ApiResources\Bag\BagEntry;
use Afeefa\ApiResources\Exception\Exceptions\MissingTypeException;

class Filter extends BagEntry
{
    public string $type;

    public string $name;

    public function __construct()
    {
        if (!isset($this->type)) {
            throw new MissingTypeException('Missing type for filter of class ' . static::class);
        };
    }

    public function toSchemaJson(): array
    {
        $json = [
            'type' => $this->type
        ];

        return $json;
    }
}