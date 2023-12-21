<?php

namespace Backend\Api;

use Neuedev\Apineu\Api\Api;
use Neuedev\Apineu\Resource\ResourceBag;
use Backend\Resources\ArticleResource;
use Backend\Resources\AuthorResource;
use Backend\Resources\TagResource;
use Backend\Types\AuthorType;

class FrontendApi extends Api
{
    protected function resources(ResourceBag $resources): void
    {
        $resources
            ->add(ArticleResource::class)

            ->add(TagResource::class)

            ->add(function (AuthorResource $resource) {
                $resource->allowActions([
                    'get_authors',
                    'create_author'
                ])
                // $resource->removeAction('get_authors');
            });
    }

    protected function types(TypeBag $types): void
    {
        $types->get(function (AuthorType $authorType) {
            $authorType->fields(function (FieldBag $fields) {
            });
        });
    }
}
