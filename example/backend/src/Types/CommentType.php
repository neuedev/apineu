<?php

namespace Backend\Types;

use Neuedev\Apineu\Field\FieldBag;
use Neuedev\Apineu\Field\Fields\DateAttribute;
use Neuedev\Apineu\Field\Fields\StringAttribute;
use Neuedev\Apineu\Type\Type;

class CommentType extends Type
{
    protected static string $type = 'Example.CommentType';

    protected function fields(FieldBag $fields): void
    {
        $fields
            ->attribute('author_name', StringAttribute::class)

            ->attribute('content', StringAttribute::class)

            ->attribute('date', DateAttribute::class)

            ->relation('article', ArticleType::class);
    }
}
