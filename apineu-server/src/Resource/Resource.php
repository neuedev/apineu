<?php

namespace Neuedev\Apineu\Resource;

use Neuedev\Apineu\Action\Action;
use Neuedev\Apineu\Action\ActionBag;
use Neuedev\Apineu\Bag\BagEntry;
use Neuedev\Apineu\Utils\HasStaticTypeTrait;

class Resource extends BagEntry
{
    use HasStaticTypeTrait;

    protected ActionBag $actions;

    public function created(): void
    {
        $this->actions = $this->container->create(ActionBag::class);
        $this->actions($this->actions);
    }

    public function getAction(string $name): Action
    {
        return $this->actions->get($name);
    }

    public function getActions(): ActionBag
    {
        return $this->actions;
    }

    public function toSchemaJson(): array
    {
        return $this->actions->toSchemaJson();
    }

    protected function actions(ActionBag $actions): void
    {
    }
}
