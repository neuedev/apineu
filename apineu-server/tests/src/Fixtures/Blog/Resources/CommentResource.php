<?php

namespace Neuedev\Apineu\Test\Fixtures\Blog\Resources;

use Neuedev\Apineu\Eloquent\ModelResource;
use Neuedev\Apineu\Test\Fixtures\Blog\Types\CommentType;

class CommentResource extends ModelResource
{
    protected static string $type = 'Blog.CommentResource';

    public string $ModelTypeClass = CommentType::class;
}
