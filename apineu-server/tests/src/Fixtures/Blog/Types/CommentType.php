<?php

namespace Neuedev\Apineu\Test\Fixtures\Blog\Types;

use Neuedev\Apineu\Eloquent\ModelType;
use Neuedev\Apineu\Field\FieldBag;
use Neuedev\Apineu\Field\Fields\StringAttribute;
use Neuedev\Apineu\Test\Fixtures\Blog\Models\Comment;
use Neuedev\Apineu\Type\Type;

class CommentType extends ModelType
{
    protected static string $type = 'Blog.Comment';

    public static string $ModelClass = Comment::class;

    protected function fields(FieldBag $fields): void
    {
        $fields
            ->attribute('text', StringAttribute::class)

            ->relation('owner', [AuthorType::class, ArticleType::class]);
    }

    protected function updateFields(FieldBag $updateFields): void
    {
        $updateFields
            ->attribute('text', StringAttribute::class)

            ->relation('owner', Type::link([AuthorType::class, ArticleType::class]));
    }

    protected function createFields(FieldBag $createFields, FieldBag $updateFields): void
    {
        $createFields
            ->from($updateFields, 'text', function (StringAttribute $attribute) {
                $attribute->required();
            })

            ->from($updateFields, 'owner');
    }
}
