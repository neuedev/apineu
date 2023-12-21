<?php

namespace Neuedev\Apineu\Tests\Api\Schema;

use Closure;
use Neuedev\Apineu\Action\Action;
use Neuedev\Apineu\Api\Api;
use Neuedev\Apineu\Exception\Exceptions\MissingTypeException;
use Neuedev\Apineu\Field\FieldBag;
use Neuedev\Apineu\Field\Fields\StringAttribute;
use Neuedev\Apineu\Test\ApiBuilder;

use Neuedev\Apineu\Test\ApiResourcesTest;

use function Neuedev\Apineu\Test\createApiWithSingleResource;
use function Neuedev\Apineu\Test\T;
use Neuedev\Apineu\Validator\Validators\StringValidator;

class SchemaApiTest extends ApiResourcesTest
{
    public function test_simple()
    {
        $api = $this->createApiWithType(
            'Test.Type',
            function (FieldBag $fields) {
                $fields
                    ->attribute('title', function (StringAttribute $attribute) {
                        $attribute->validate(function (StringValidator $v) {
                            $v
                                ->filled()
                                ->min(2)
                                ->max(100);
                        });
                    });
            }
        );

        $schema = $api->toSchemaJson();

        $expectedApiSchema = [
            'type' => 'Test.Api',
            'resources' => [
                'Test.Resource' => [
                    'test_action' => [
                        'response' => [
                            'type' => 'Test.Type'
                        ]
                    ]
                ]
            ],
            'types' => [
                'Test.Type' => [
                    'fields' => [
                        'title' => [
                            'type' => 'Afeefa.StringAttribute',
                            'validator' => [
                                'type' => 'Afeefa.StringValidator',
                                'params' => [
                                    'filled' => true,
                                    'min' => 2,
                                    'max' => 100
                                ]
                            ]
                        ]
                    ],
                    'update_fields' => [],
                    'create_fields' => []
                ]
            ],
            'validators' => [
                'Afeefa.StringValidator' => [
                    'sanitizers' => [
                        'trim' => [
                            'default' => true
                        ],
                        'collapseWhite' => [
                            'default' => true
                        ],
                        'emptyNull' => [
                            'default' => true
                        ]
                    ],
                    'rules' => [
                        'string' => [
                            'message' => '{{ fieldLabel }} sollte eine Zeichenkette sein.',
                            'default' => true
                        ],
                        'min' => [
                            'message' => '{{ fieldLabel }} sollte mindestens {{ param }} Zeichen beinhalten.'
                        ],
                        'max' => [
                            'message' => '{{ fieldLabel }} sollte maximal {{ param }} Zeichen beinhalten.'
                        ],
                        'filled' => [
                            'message' => '{{ fieldLabel }} sollte einen Wert enthalten.'
                        ],
                        'regex' => [
                            'message' => '{{ fieldLabel }} sollte ein gültiger Wert sein.'
                        ]
                    ]
                ]
            ]
        ];

        $this->assertEquals($expectedApiSchema, $schema);
    }

    public function test_get_type_with_missing_type()
    {
        $this->expectException(MissingTypeException::class);
        $this->expectExceptionMessageMatches('/^Missing type for class Neuedev\\\Apineu\\\Test\\\TestApi@anonymous/');

        $api = (new ApiBuilder())->api()->get();

        $api::type();
    }

    public function test_schema_with_missing_type()
    {
        $this->expectException(MissingTypeException::class);
        $this->expectExceptionMessageMatches('/^Missing type for class Neuedev\\\Apineu\\\Test\\\TestApi@anonymous/');

        $api = (new ApiBuilder())->api()->get();

        $api->toSchemaJson();
    }

    private function createApiWithType(
        string $typeName,
        ?Closure $fieldsCallback = null,
        ?Closure $addActionCallback = null
    ): Api {
        $this->typeBuilder()->type($typeName, $fieldsCallback)->get();

        if (!$addActionCallback) {
            $addActionCallback = function (Closure $addAction, Closure $addQuery) use ($typeName) {
                $addQuery('test_action', T($typeName), function (Action $action) use ($typeName) {
                    $action
                        ->resolve(function () {
                        });
                });
            };
        }

        return createApiWithSingleResource($addActionCallback);
    }
}
