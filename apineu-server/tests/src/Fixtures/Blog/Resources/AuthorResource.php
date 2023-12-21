<?php

namespace Neuedev\Apineu\Test\Fixtures\Blog\Resources;

use Neuedev\Apineu\Eloquent\ModelResource;
use Neuedev\Apineu\Test\Fixtures\Blog\Types\AuthorType;

class AuthorResource extends ModelResource
{
    protected static string $type = 'Blog.AuthorResource';

    public string $ModelTypeClass = AuthorType::class;
}
