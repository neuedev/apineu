<?php

namespace Neuedev\Apineu\Resource;

use Neuedev\Apineu\Bag\Bag;
use Neuedev\Apineu\Bag\BagEntryInterface;

use function Neuedev\Apineu\DI\classOrCallback;
use function Neuedev\Apineu\DI\getCallbackArgumentType;

/**
 * @method Resource get(string $name, Closure $callback)
 * @method Resource[] getEntries()
 */
class ResourceBag extends Bag
{
    public function add($classOrCallback): ResourceBag
    {
        [$ResourceClass, $callback] = classOrCallback($classOrCallback);
        if ($callback) { // callback and no resource class given
            $ResourceClass = getCallbackArgumentType($callback);
        }

        $this->setDefinition($ResourceClass::type(), $classOrCallback);
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
