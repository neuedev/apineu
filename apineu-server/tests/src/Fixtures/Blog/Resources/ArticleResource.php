<?php

namespace Neuedev\Apineu\Test\Fixtures\Blog\Resources;

use Neuedev\Apineu\Eloquent\ModelResource;
use Neuedev\Apineu\Test\Fixtures\Blog\Types\ArticleType;

class ArticleResource extends ModelResource
{
    protected static string $type = 'Blog.ArticleResource';

    public string $ModelTypeClass = ArticleType::class;
}
