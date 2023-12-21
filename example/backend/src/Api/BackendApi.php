<?php

namespace Backend\Api;

use Backend\Resources\AppResource;
use Backend\Resources\ArticleResource;
use Backend\Resources\AuthorResource;
use Backend\Resources\TagResource;
use Backend\Types\AuthorType;
use Neuedev\Apineu\Api\Api;
use Neuedev\Apineu\Resource\ResourceBag;

class BackendApi extends Api
{
    protected static string $type = 'Example.BackendApi';

    protected function resources(ResourceBag $resources): void
    {
        $resources
            ->add(AppResource::class)
            ->add(ArticleResource::class)
            ->add(TagResource::class)
            ->add(function (AuthorResource $resource) {
                // $resource->removeAction('get_authors');
            });
    }

    // protected function types(TypeBag $types): void
    // {
    //     $types->get(function (AuthorType $authorType) {
    //         $authorType->removeRelation('articles');
    //     });
    // }
}
