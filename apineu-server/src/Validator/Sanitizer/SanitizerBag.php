<?php

namespace Neuedev\Apineu\Validator\Sanitizer;

use Neuedev\Apineu\Bag\Bag;
use Neuedev\Apineu\Bag\BagEntryInterface;

use stdClass;

/**
 * @method Sanitizer get(string $name, Closure $callback)
 * @method Sanitizer[] getEntries()
 */
class SanitizerBag extends Bag
{
    public function add(string $name): Sanitizer
    {
        $sanitizer = new Sanitizer();
        $this->setInternal($name, $sanitizer);
        return $sanitizer;
    }

    /**
     * disabled
     */
    public function set(string $name, BagEntryInterface $value): Bag
    {
        return $this;
    }

    public function toSchemaJson(): array
    {
        return array_map(function (BagEntryInterface $entry) {
            $schema = $entry->toSchemaJson();
            return empty($schema) ? new stdClass() : $schema;
        }, $this->getEntries());
    }
}
