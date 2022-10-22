<?php

namespace Afeefa\ApiResources\Model;

class SimpleModel extends Model
{
    public function jsonSerialize(): mixed
    {
        foreach ($this as $name => $value) {
            if ($name === 'visibleFields') {
                continue;
            }
            $this->visibleFields[] = $name;
        }

        $json = parent::jsonSerialize();

        return $json;
    }
}
