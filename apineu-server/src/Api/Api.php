<?php

namespace Neuedev\Apineu\Api;

use Closure;
use Neuedev\Apineu\Action\Action;
use Neuedev\Apineu\DI\ContainerAwareInterface;
use Neuedev\Apineu\DI\ContainerAwareTrait;
use Neuedev\Apineu\Resource\Resource;
use Neuedev\Apineu\Resource\ResourceBag;
use Neuedev\Apineu\Type\TypeClassMap;
use Neuedev\Apineu\Utils\HasStaticTypeTrait;

class Api implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use ToSchemaJsonTrait;
    use HasStaticTypeTrait;

    protected bool $debug = false;

    protected ResourceBag $resources;

    protected array $AdditionalValidatorClasses = [];

    public function created(): void
    {
        $this->container->registerAlias($this, self::class);

        $this->resources = $this->container->create(ResourceBag::class);
        $this->resources($this->resources);
    }

    public function debug($debug = true): static
    {
        $this->debug = $debug;
        return $this;
    }

    public function getDebug(): bool
    {
        return $this->debug;
    }

    public function getResources(): ResourceBag
    {
        return $this->resources;
    }

    public function getResource(string $resourceType): Resource
    {
        return $this->resources->get($resourceType);
    }

    public function getAction(string $resourceType, string $actionName): Action
    {
        $resource = $this->resources->get($resourceType);
        return $resource->getAction($actionName);
    }

    public function request(Closure $callback)
    {
        /** @var ApiRequest */
        $request = $this->container->get(ApiRequest::class);
        $request->api($this);
        $callback($request);
        return $request->dispatch();
    }

    public function requestFromInput(?array $input = null): array
    {
        /** @var ApiRequest */
        $request = $this->container->get(ApiRequest::class);
        $request->api($this);
        $request->fromInput($input);
        return $request->dispatch();
    }

    public function registerValidator(string $ValidatorClass): static
    {
        $this->AdditionalValidatorClasses[] = $ValidatorClass;
        return $this;
    }

    public function toSchemaJson(): array
    {
        $resources = $this->resources->toSchemaJson();
        $usedTypes = $this->container->get(TypeClassMap::class)
            ->createUsedTypesForApi($this);
        $usedValidators = $this->createAllUsedValidators($usedTypes);

        // debug_dump(array_keys($usedTypes));
        // debug_dump(array_keys($usedValidators));

        // debug_dump($typeClassMap);
        // $this->container->dumpEntries();

        $types = [];
        foreach ($usedTypes as $type) {
            $types[$type::type()] = $type->toSchemaJson();
        }

        $validators = [];
        foreach ($usedValidators as $validator) {
            $validators[$validator::type()] = $validator->toSchemaJson();
            unset($validators[$validator::type()]['params']);
            unset($validators[$validator::type()]['type']);
        }

        return [
            'type' => $this::type(),
            'resources' => $resources,
            'types' => $types,
            'validators' => $validators
        ];
    }

    protected function resources(ResourceBag $resources): void
    {
    }

    /**
     * @param Type[] $types
     */
    protected function createAllUsedValidators(array $types): array
    {
        $validators = [];

        foreach ($types as $type) {
            $ValidatorClasses = [
                ...$this->AdditionalValidatorClasses,
                ...$type->getAllValidatorClasses()
            ];
            foreach ($ValidatorClasses as $ValidatorClass) {
                if (!isset($validators[$ValidatorClass])) {
                    $validators[$ValidatorClass] = $this->container->get($ValidatorClass);
                }
            }
        }

        return $validators;
    }
}
