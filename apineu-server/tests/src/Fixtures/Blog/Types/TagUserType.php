<?php

namespace Neuedev\Apineu\Test\Fixtures\Blog\Types;

use Neuedev\Apineu\Eloquent\ModelType;
use Neuedev\Apineu\Field\FieldBag;
use Neuedev\Apineu\Test\Fixtures\Blog\Models\TagUser;

class TagUserType extends ModelType
{
    protected static string $type = 'Blog.TagUser';

    public static string $ModelClass = TagUser::class;

    protected function fields(FieldBag $fields): void
    {
        $fields
            ->relation('user', [AuthorType::class, ArticleType::class]);
    }
}
