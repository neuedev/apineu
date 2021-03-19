<?php

namespace Afeefa\ApiResources\Field;

use Afeefa\ApiResources\Api\TypeRegistry;
use Afeefa\ApiResources\Bag\BagEntry;
use Afeefa\ApiResources\DI\Resolver;
use Afeefa\ApiResources\Exception\Exceptions\MissingTypeException;
use Afeefa\ApiResources\Validator\Validator;
use Closure;

class Field extends BagEntry
{
    public static string $type;

    protected string $name;

    protected ?Validator $validator = null;

    protected bool $required = false;

    protected bool $allowed = false;

    public function created(): void
    {
        if (!static::$type) {
            throw new MissingTypeException('Missing type for field of class ' . static::class);
        };
    }

    public function name(string $name): Field
    {
        $this->name = $name;
        return $this;
    }

    public function validate(Closure $callback): Field
    {
        if ($this->validator) {
            $this->container->call(
                $callback,
                function (Resolver $r) {
                    $r
                        ->fix($this->validator)
                        ->resolved(function (Validator $validator) {
                            $this->container->get(function (TypeRegistry $typeRegistry) use ($validator) {
                                $typeRegistry->registerValidator(get_class($validator));
                            });
                        });
                }
            );
        } else {
            $this->container->call(
                $callback,
                function (Resolver $r) {
                    $r
                        ->create()
                        ->resolved(function (Validator $validator) {
                            $this->validator = $validator;

                            $this->container->get(function (TypeRegistry $typeRegistry) use ($validator) {
                                $typeRegistry->registerValidator(get_class($validator));
                            });
                        });
                }
            );
        }

        return $this;
    }

    public function validator(Validator $validator): Field
    {
        $this->validator = $validator;
        return $this;
    }

    public function required(bool $required = true): Field
    {
        $this->required = $required;
        return $this;
    }

    public function allowed(): Field
    {
        $this->allowed = true;
        return $this;
    }

    public function isAllowed(): bool
    {
        return $this->allowed;
    }

    public function clone(): Field
    {
        return $this->container->create(static::class, function (Resolver $r) {
            $r->resolved(function (Field $field) {
                $field
                    ->name($this->name)
                    ->required($this->required);
                if ($this->validator) {
                    $field->validator($this->validator->clone());
                }
            });
        });
    }

    public function toSchemaJson(): array
    {
        $json = [
            'type' => static::$type,
            // 'name' => $this->name
        ];

        if ($this->required) {
            $json['required'] = true;
        }

        if ($this->validator) {
            $json['validator'] = $this->validator->toSchemaJson();
            unset($json['validator']['rules']);
        }

        return $json;
    }
}
