<?php

namespace Neuedev\Apineu\Test\Fixtures\Blog\Resources;

use Neuedev\Apineu\Eloquent\ModelResource;
use Neuedev\Apineu\Test\Fixtures\Blog\Types\TagType;

class TagResource extends ModelResource
{
    protected static string $type = 'Blog.TagResource';

    public string $ModelTypeClass = TagType::class;
}
