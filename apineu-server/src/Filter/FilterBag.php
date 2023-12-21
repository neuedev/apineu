<?php

namespace Neuedev\Apineu\Filter;

use Neuedev\Apineu\Bag\Bag;
use Neuedev\Apineu\Bag\BagEntryInterface;

/**
 * @method Filter get(string $name, Closure $callback)
 * @method Filter[] getEntries()
 */
class FilterBag extends Bag
{
    public function add(string $name, $classOrCallback): FilterBag
    {
        return $this->addAfter(null, $name, $classOrCallback);
    }

    public function addAfter(?string $after, string $name, $classOrCallback): FilterBag
    {
        $this->container->create($classOrCallback, function (Filter $filter) use ($name, $after) {
            $filter->name($name);
            $this->setInternal($name, $filter, $after);
        });

        return $this;
    }

    /**
     * disabled
     */
    public function set(string $name, BagEntryInterface $value): Bag
    {
        return $this;
    }
}
