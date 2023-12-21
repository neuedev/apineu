<?php

namespace Neuedev\Apineu\Resolver\Field;

use Neuedev\Apineu\Model\ModelInterface;
use Neuedev\Apineu\Resolver\Base\BaseResolver;

class BaseFieldResolver extends BaseResolver
{
    /**
     * @var ModelInterface[]
     */
    protected array $owners = [];

    public function addOwner(ModelInterface $owner): static
    {
        $this->owners[] = $owner;
        return $this;
    }

    public function addOwners(array $owner): static
    {
        $this->owners = $owner;
        return $this;
    }

    /**
     * @return ModelInterface[]
     */
    public function getOwners(): array
    {
        return $this->owners;
    }
}
