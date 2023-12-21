<?php

namespace Neuedev\Apineu\Resolver\Field;

use Neuedev\Apineu\Field\Attribute;

trait AttributeResolverTrait
{
    protected Attribute $attribute;

    public function attribute(Attribute $attribute): self
    {
        $this->attribute = $attribute;
        return $this;
    }

    public function getAttribute(): Attribute
    {
        return $this->attribute;
    }

    public function getResolveParam(string $name)
    {
        return $this->attribute->getResolveParam($name);
    }
}
