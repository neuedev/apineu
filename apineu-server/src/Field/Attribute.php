<?php

namespace Neuedev\Apineu\Field;

use Neuedev\Apineu\Utils\HasStaticTypeTrait;

class Attribute extends Field
{
    use HasStaticTypeTrait;

    public function toSchemaJson(): array
    {
        $json = parent::toSchemaJson();

        if ($this->isMutation && $this->hasDefaultValue()) {
            $json['default'] = $this->default;
        }

        return $json;
    }
}
