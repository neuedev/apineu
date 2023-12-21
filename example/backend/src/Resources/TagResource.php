<?php

namespace Backend\Resources;

use Backend\Resolvers\TagsResolver;
use Backend\Types\TagType;
use Neuedev\Apineu\Action\Action;
use Neuedev\Apineu\Action\ActionBag;
use Neuedev\Apineu\Resource\Resource;
use Neuedev\Apineu\Type\Type;

class TagResource extends Resource
{
    protected static string $type = 'Example.TagResource';

    protected function actions(ActionBag $actions): void
    {
        $actions->query('get_tags', Type::list(TagType::class), function (Action $action) {
            $action->resolve([TagsResolver::class, 'get_tags']);
        });
    }
}
