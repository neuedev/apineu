<?php

namespace Neuedev\Apineu\Test\Fixtures\Blog\Types;

use Neuedev\Apineu\Eloquent\ModelType;
use Neuedev\Apineu\Field\FieldBag;
use Neuedev\Apineu\Field\Fields\StringAttribute;
use Neuedev\Apineu\Test\Fixtures\Blog\Models\Link;

class LinkType extends ModelType
{
    protected static string $type = 'Blog.Link';

    public static string $ModelClass = Link::class;

    protected function fields(FieldBag $fields): void
    {
        $fields->attribute('url', StringAttribute::class);
    }

    protected function updateFields(FieldBag $updateFields): void
    {
        $updateFields->attribute('url', StringAttribute::class);
    }

    protected function createFields(FieldBag $createFields, FieldBag $updateFields): void
    {
        $createFields->from($updateFields, 'url', function (StringAttribute $attribute) {
            $attribute->required();
        });
    }
}
