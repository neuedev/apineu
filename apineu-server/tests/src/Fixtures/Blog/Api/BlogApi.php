<?php

namespace Neuedev\Apineu\Test\Fixtures\Blog\Api;

use Neuedev\Apineu\Api\Api;
use Neuedev\Apineu\Resource\ResourceBag;
use Neuedev\Apineu\Test\Fixtures\Blog\Resources\AppResource;
use Neuedev\Apineu\Test\Fixtures\Blog\Resources\ArticleResource;
use Neuedev\Apineu\Test\Fixtures\Blog\Resources\AuthorResource;
use Neuedev\Apineu\Test\Fixtures\Blog\Resources\CommentResource;
use Neuedev\Apineu\Test\Fixtures\Blog\Resources\TagResource;

class BlogApi extends Api
{
    protected static string $type = 'Blog.BlogApi';

    protected function resources(ResourceBag $resources): void
    {
        $resources
            ->add(AppResource::class)
            ->add(ArticleResource::class)
            ->add(TagResource::class)
            ->add(AuthorResource::class)
            ->add(CommentResource::class);
    }
}
