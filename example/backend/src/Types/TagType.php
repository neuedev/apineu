<?php

namespace Backend\Types;

use Backend\Resolvers\TagsResolver;
use Neuedev\Apineu\Field\FieldBag;
use Neuedev\Apineu\Field\Fields\StringAttribute;
use Neuedev\Apineu\Field\Relation;
use Neuedev\Apineu\Type\Type;

class TagType extends Type
{
    protected static string $type = 'Example.TagType';

    protected function fields(FieldBag $fields): void
    {
        $fields
            ->attribute('name', StringAttribute::class)

            ->relation('users', Type::list([AuthorType::class, ArticleType::class]), function (Relation $relation) {
                $relation->resolve([TagsResolver::class, 'resolve_tag_users_relation']);
            });
    }
}

// tags: {
//     fields: [
//         'name',
//         {
//             users: [
//                 id, type, {
//                     'Example.Article': [name, title, summary],
//                     'Example.Author': [name, password]
//                 }
//             ]
//         }
//     ]
// }
