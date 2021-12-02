<?php

namespace Afeefa\ApiResources\Resolver;

use Afeefa\ApiResources\Exception\Exceptions\InvalidConfigurationException;
use Afeefa\ApiResources\Exception\Exceptions\MissingCallbackException;
use Afeefa\ApiResources\Model\ModelInterface;
use Afeefa\ApiResources\Type\Type;
use Closure;
use Generator;

/**
 * @method QueryRelationResolver ownerType(Type $ownerType)
 * @method QueryRelationResolver relation(Relation $relation)
 */
class QueryRelationResolver extends BaseRelationResolver
{
    protected array $fields;

    /**
     * Closure or array
     */
    protected $ownerIdFields;

    protected ?Closure $loadCallback = null;

    protected ?Closure $mapCallback = null;

    public function fields(array $fields): QueryRelationResolver
    {
        $this->fields = $fields;
        return $this;
    }

    public function getRequestedFields(?string $typeName = null): array
    {
        $relationName = $this->field->getName();

        $typeName = $this->validateRequestedType(
            $this->getRelation()->getRelatedType(),
            $typeName,
            "You need to pass a type name to getRequestedFields() in the resolver of relation {$relationName} since the relation returns an union type",
            "The type name passed to getRequestedFields() in the resolver of relation {$relationName} is not supported by the relation"
        );

        return $this->getResolveContext($typeName, $this->fields)
            ->getRequestedFields();
    }

    public function getSelectFields(?string $typeName = null): array
    {
        $relationName = $this->field->getName();

        $typeName = $this->validateRequestedType(
            $this->getRelation()->getRelatedType(),
            $typeName,
            "You need to pass a type name to getSelectFields() in the resolver of relation {$relationName} since the relation returns an union type",
            "The type name passed to getSelectFields() in the resolver of relation {$relationName} is not supported by the relation"
        );

        return $this->getResolveContext($typeName, $this->fields)
            ->getSelectFields($typeName);
    }

    public function ownerIdFields($ownerIdFields): QueryRelationResolver
    {
        $this->ownerIdFields = $ownerIdFields;
        return $this;
    }

    public function getOwnerIdFields(): array
    {
        if ($this->ownerIdFields instanceof Closure) {
            return ($this->ownerIdFields)() ?? [];
        }

        return $this->ownerIdFields ?? [];
    }

    public function load(Closure $callback): QueryRelationResolver
    {
        $this->loadCallback = $callback;
        return $this;
    }

    public function map(Closure $callback): QueryRelationResolver
    {
        $this->mapCallback = $callback;
        return $this;
    }

    public function resolve(): void
    {
        // if error
        $relationName = $this->field->getName();
        $resolverForRelation = "Resolver for relation {$relationName}";

        // query db

        $loadCallback = $this->loadCallback;
        if (!$loadCallback) {
            throw new MissingCallbackException("{$resolverForRelation} needs to implement a load() method.");
        }
        $loadResult = $loadCallback($this->owners);

        if ($loadResult instanceof Generator) {
            $loadResult = iterator_to_array($loadResult, false);
        }

        if (!is_array($loadResult)) {
            throw new InvalidConfigurationException("{$resolverForRelation} needs to return an array from its load() method.");
        }

        $models = [];

        // map results to owners

        // this is just a nifty one-liner to detect if there are non-models in the given array
        $isAllModels = fn ($array) => !count(array_filter($array, fn ($elm) => !$elm instanceof ModelInterface));

        if (isset($this->mapCallback)) {
            $relationName = $this->field->getName();
            foreach ($this->owners as $owner) {
                $value = ($this->mapCallback)($loadResult, $owner);
                $owner->apiResourcesSetRelation($relationName, $value);
                $models[] = $value;
            }

            if ($this->field->getRelatedType()->isList()) {
                $models = array_merge(...$models); // make flat
                if (!$isAllModels($models)) {
                    throw new InvalidConfigurationException("{$resolverForRelation} needs to return an array of ModelInterface objects from its map() method.");
                }
            } else {
                if (!$isAllModels($models)) {
                    throw new InvalidConfigurationException("{$resolverForRelation} needs to return a ModelInterface object or null from its map() method.");
                }
            }
        } else {
            $models = $loadResult;
            if (!$isAllModels($models)) {
                throw new InvalidConfigurationException("{$resolverForRelation} needs to return an array of ModelInterface objects from its load() method.");
            }
        }

        $this->resolveModels($models, $this->fields);
    }
}
