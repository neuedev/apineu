<?php

namespace Backend\Types;

use Backend\Resolvers\AuthorsResolver;
use Backend\Resolvers\CommentsResolver;
use Backend\Resolvers\TagsResolver;
use Backend\Resources\AuthorResource;
use Neuedev\Apineu\Api\ApiRequest;
use Neuedev\Apineu\Field\Attribute;
use Neuedev\Apineu\Field\FieldBag;
use Neuedev\Apineu\Field\Fields\DateAttribute;
use Neuedev\Apineu\Field\Fields\StringAttribute;
use Neuedev\Apineu\Field\Relation;
use Neuedev\Apineu\Type\Type;
use Neuedev\Apineu\Validator\Validators\LinkOneValidator;
use Neuedev\Apineu\Validator\Validators\StringValidator;

class ArticleType extends Type
{
    protected static string $type = 'Example.Article';

    // protected function translations(): array
    // {
    //     return [
    //         'TITLE_SINGULAR' => 'Artikel',
    //         'TITLE_PLURAL' => 'Artikel',
    //         'TITLE_EMPTY' => 'Kein Titel',
    //         'TITLE_NEW' => 'Neuer Artikel'
    //     ];
    // }

    protected function fields(FieldBag $fields): void
    {
        $fields->attribute('title', StringAttribute::class)

            ->attribute('summary', StringAttribute::class)

            ->attribute('content', StringAttribute::class)

            ->attribute('date', DateAttribute::class)

            ->relation('author', AuthorType::class, function (Relation $relation) {
                $relation->resolve([AuthorsResolver::class, 'resolve_author_relation']);
            })

            ->relation('comments', Type::list(CommentType::class), function (Relation $relation) {
                $relation->resolve([CommentsResolver::class, 'resolve_comments_relation']);
            })

            ->relation('tags', Type::list(TagType::class), function (Relation $relation) {
                $relation->resolve([TagsResolver::class, 'resolve_tags_relation']);
            });
    }

    protected function updateFields(FieldBag $updateFields): void
    {
        $updateFields
            ->attribute('title', function (StringAttribute $attribute) {
                $attribute
                    ->validate(function (StringValidator $v) {
                        $v
                            ->filled()
                            ->min(5)
                            ->max(101);
                    });
            })

            ->attribute('summary', function (StringAttribute $attribute) {
                $attribute
                    ->validate(function (StringValidator $v) {
                        $v
                            ->min(3)
                            ->max(200);
                    });
            })

            ->attribute('content', StringAttribute::class)

            ->attribute('date', DateAttribute::class)

            ->relation('author', Type::link(AuthorType::class), function (Relation $relation) {
                $relation
                    ->required()
                    ->validate(function (LinkOneValidator $v) {
                        $v->filled();
                    })
                    ->resolve([AuthorsResolver::class, 'resolve_save_author_relation'])
                    ->optionsRequest(function (ApiRequest $request) {
                        $request
                            ->resourceType(AuthorResource::type())
                            ->actionName('get_authors')
                            ->fields(['name' => true, 'count_articles' => true])
                            ->filters(['page_size' => 100]);
                    });
            })

            ->relation('tags', Type::list(Type::link(TagType::class)));
    }

    protected function createFields(FieldBag $createFields, FieldBag $updateFields): void
    {
        $createFields
            ->from($updateFields, 'title', function (Attribute $attribute) {
                $attribute->required();
            })

            ->from($updateFields, 'author', function (Relation $relation) {
                $relation->required();
            });
    }
}
