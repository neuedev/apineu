<?php

namespace Neuedev\Apineu\Resolver\Field;

use Neuedev\Apineu\Field\Relation;

trait RelationResolverTrait
{
    protected Relation $relation;

    public function relation(Relation $relation): static
    {
        $this->relation = $relation;
        return $this;
    }

    public function getRelation(): Relation
    {
        return $this->relation;
    }

    public function getResolveParam(string $name)
    {
        return $this->relation->getResolveParam($name);
    }
}
