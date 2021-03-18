<?php

namespace Afeefa\ApiResources\Field;

use Afeefa\ApiResources\Bag\BagEntry;
use Afeefa\ApiResources\Exception\Exceptions\MissingTypeException;
use Afeefa\ApiResources\Validator\Validator;

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

    public function validate(callable $callback): Field
    {
        if ($this->validator) {
            $callback($this->validator);
        } else {
            $Validator = $this->container->getCallbackArgumentType($callback);
            $this->container->add($Validator); // register validator class
            $this->container->create($Validator, function (Validator $validator) use ($callback) {
                $callback($validator);
                $this->validator = $validator;
            });
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
        return $this->container->create(static::class, function (Field $field) {
            $field
                ->name($this->name)
                ->required($this->required);
            if ($this->validator) {
                $field->validator($this->validator->clone());
            }
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
