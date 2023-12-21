<?php

namespace Backend\Types;

use Neuedev\Apineu\Field\FieldBag;
use Neuedev\Apineu\Field\Fields\IntAttribute;
use Neuedev\Apineu\Type\Type;

class CountsType extends Type
{
    protected static string $type = 'Example.Counts';

    protected function fields(FieldBag $fields): void
    {
        $fields
            ->attribute('count_articles', IntAttribute::class)

            ->attribute('count_authors', IntAttribute::class);
    }
}
