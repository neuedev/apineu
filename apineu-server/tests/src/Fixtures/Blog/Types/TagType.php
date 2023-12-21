<?php

namespace Neuedev\Apineu\Test\Fixtures\Blog\Types;

use Neuedev\Apineu\Eloquent\ModelType;
use Neuedev\Apineu\Field\FieldBag;
use Neuedev\Apineu\Field\Fields\StringAttribute;
use Neuedev\Apineu\Test\Fixtures\Blog\Models\Tag;
use Neuedev\Apineu\Type\Type;

class TagType extends ModelType
{
    protected static string $type = 'Blog.Tag';

    public static string $ModelClass = Tag::class;

    protected function fields(FieldBag $fields): void
    {
        $fields
            ->attribute('name', StringAttribute::class)

            ->relation('tag_users', Type::list(TagUserType::class));
    }
}
