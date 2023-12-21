<?php

namespace Neuedev\Apineu\Test\Fixtures\TestApi;

use Neuedev\Apineu\Field\FieldBag;
use Neuedev\Apineu\Field\Fields\StringAttribute;
use Neuedev\Apineu\Type\Type;

class TestType extends Type
{
    protected static string $type = 'TestType';

    protected function fields(FieldBag $fields): void
    {
        $fields
            ->attribute('attr1', StringAttribute::class)
            ->attribute('attr2', StringAttribute::class)
            ->attribute('attr3', StringAttribute::class);
    }
}
