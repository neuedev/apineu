<?php

namespace Neuedev\Apineu\Resolver\Base;

use Neuedev\Apineu\DI\ContainerAwareInterface;
use Neuedev\Apineu\DI\ContainerAwareTrait;
use Neuedev\Apineu\Model\ModelInterface;
use Neuedev\Apineu\Type\Type;
use Neuedev\Apineu\Type\TypeClassMap;

class BaseResolver implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected array $resolveContexts = [];

    protected function getTypeByName(string $typeName): Type
    {
        return $this->container->call(function (TypeClassMap $typeClassMap) use ($typeName) {
            $TypeClass = $typeClassMap->get($typeName) ?? Type::class;
            return $this->container->get($TypeClass);
        });
    }

    /**
     * @param ModelInterface[] $models
     */
    protected function sortModelsByType(array $models): array
    {
        $modelsByType = [];
        foreach ($models as $model) {
            $type = $model->apiResourcesGetType();
            $modelsByType[$type][] = $model;
        }
        return $modelsByType;
    }
}
