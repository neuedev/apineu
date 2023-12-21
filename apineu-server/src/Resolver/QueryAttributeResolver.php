<?php

namespace Neuedev\Apineu\Resolver;

use Closure;
use Neuedev\Apineu\Exception\Exceptions\InvalidConfigurationException;
use Neuedev\Apineu\Exception\Exceptions\MissingCallbackException;
use Neuedev\Apineu\Resolver\Field\AttributeResolverTrait;
use Neuedev\Apineu\Resolver\Field\BaseFieldResolver;
use Neuedev\Apineu\Resolver\Query\QueryResolverTrait;

class QueryAttributeResolver extends BaseFieldResolver
{
    use QueryResolverTrait;
    use AttributeResolverTrait;

    protected array $selectFields = [];

    protected ?Closure $selectCallback = null;

    protected ?Closure $mapCallback = null;

    public function select($selectFields, ?Closure $selectCallback = null): QueryAttributeResolver
    {
        $this->selectFields = is_array($selectFields)
            ? $selectFields
            : [$selectFields];
        $this->selectCallback = $selectCallback;
        return $this;
    }

    public function getSelectFields(): array
    {
        return $this->selectFields;
    }

    public function map(Closure $callback): QueryAttributeResolver
    {
        $this->mapCallback = $callback;
        return $this;
    }

    public function resolve()
    {
        // if error
        $attributeName = $this->attribute->getName();
        $resolverForAttribute = "Resolver for attribute {$attributeName}";

        // query db

        if (!$this->getCallback) {
            if (count($this->selectFields)) {
                if ($this->selectCallback) {
                    foreach ($this->owners as $owner) {
                        $value = ($this->selectCallback)($owner);
                        $owner->apiResourcesSetAttribute($attributeName, $value);
                    }
                }
                return; // only select fields are set up
            }
            throw new MissingCallbackException("{$resolverForAttribute} needs to implement a get() method.");
        }
        $objects = ($this->getCallback)($this->owners);

        // map results to owners

        if ($this->mapCallback) {
            if (!is_array($objects)) {
                throw new InvalidConfigurationException("{$resolverForAttribute} needs to return an array if map() is used.");
            }

            foreach ($this->owners as $owner) {
                $value = ($this->mapCallback)($objects, $owner);
                $owner->apiResourcesSetAttribute($attributeName, $value);
            }
        }
    }
}
