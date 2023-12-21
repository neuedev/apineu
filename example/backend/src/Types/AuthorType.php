<?php

namespace Backend\Types;

use Backend\Resolvers\ArticlesResolver;
use Backend\Resolvers\TagsResolver;
use Neuedev\Apineu\Field\FieldBag;
use Neuedev\Apineu\Field\Fields\StringAttribute;
use Neuedev\Apineu\Field\Relation;
use Neuedev\Apineu\Type\Type;
use Neuedev\Apineu\Validator\Validators\StringValidator;

class AuthorType extends Type
{
    protected static string $type = 'Example.Author';

    // protected function translations(): array
    // {
    //     return [
    //         'TITLE_SINGULAR' => 'Autor:in',
    //         'TITLE_PLURAL' => 'Autor:innen',
    //         'TITLE_EMPTY' => 'Kein Name',
    //         'TITLE_NEW' => 'Neue Autor:in'
    //     ];
    // }

    protected function fields(FieldBag $fields): void
    {
        $fields
            ->attribute('name', StringAttribute::class)

            ->attribute('email', StringAttribute::class)

            ->relation('articles', Type::list(ArticleType::class), function (Relation $relation) {
                $relation
                    ->restrictTo(Relation::RESTRICT_TO_COUNT)
                    ->resolve([ArticlesResolver::class, 'resolve_articles_relation']);
            })

            ->relation('tags', Type::list(TagType::class), function (Relation $relation) {
                $relation->resolve([TagsResolver::class, 'resolve_tags_relation']);
            });
    }

    protected function updateFields(FieldBag $updateFields): void
    {
        $updateFields->attribute('name', function (StringAttribute $attribute) {
            $attribute->validate(function (StringValidator $v) {
                $v
                    ->filled()
                    ->min(5)
                    ->max(101);
            });
        });
    }

    protected function createFields(FieldBag $createFields, FieldBag $updateFields): void
    {
        $createFields->from($updateFields, 'name');
    }
}
