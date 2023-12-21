<?php

namespace Neuedev\Apineu\Tests\Api\Schema;

use Neuedev\Apineu\Exception\Exceptions\MissingTypeException;
use Neuedev\Apineu\Field\FieldBag;

use Neuedev\Apineu\Field\Fields\StringAttribute;
use Neuedev\Apineu\Test\ApiResourcesTest;

use function Neuedev\Apineu\Test\createApiWithSingleType;

use Neuedev\Apineu\Test\TestValidator;

use Neuedev\Apineu\Test\ValidatorBuilder;
use Neuedev\Apineu\Validator\Rule\RuleBag;

class SchemaValidatorTest extends ApiResourcesTest
{
    public function test_simple()
    {
        /** @var TestValidator */
        $validator = (new ValidatorBuilder())
            ->validator(
                'Test.Validator',
                function (RuleBag $rules) {
                    $rules
                        ->add('min')
                        ->message('{{ fieldLabel }} should be greater than {{ param }}.');

                    $rules
                        ->add('max')
                        ->message('{{ fieldLabel }} should be lesser than {{ param }}.');
                }
            )
            ->get();

        $api = createApiWithSingleType(
            'Test.Type',
            function (FieldBag $fields) use ($validator) {
                $fields
                    ->attribute('title', function (StringAttribute $attribute) use ($validator) {
                        $attribute->validate($validator->min(4)->max(14));
                    });
            }
        );

        $schema = $api->toSchemaJson();

        $expectedTypesSchema = [
            'Test.Type' => [
                'fields' => [
                    'title' => [
                        'type' => 'Afeefa.StringAttribute',
                        'validator' => [
                            'type' => 'Test.Validator',
                            'params' => [
                                'min' => 4,
                                'max' => 14
                            ]
                        ]
                    ]
                ],
                'update_fields' => [],
                'create_fields' => []
            ]
        ];

        $this->assertEquals($expectedTypesSchema, $schema['types']);

        $expectedValidatorsSchema = [
            'Test.Validator' => [
                'rules' => [
                    'filled' => [
                        'message' => '{{ fieldLabel }} sollte einen Wert enthalten.'
                    ],
                    'min' => [
                        'message' => '{{ fieldLabel }} should be greater than {{ param }}.'
                    ],
                    'max' => [
                        'message' => '{{ fieldLabel }} should be lesser than {{ param }}.'
                    ]
                ]
            ]
        ];

        $this->assertEquals($expectedValidatorsSchema, $schema['validators']);
    }

    public function test_get_type_with_missing_type()
    {
        $this->expectException(MissingTypeException::class);
        $this->expectExceptionMessageMatches('/^Missing type for class Neuedev\\\Apineu\\\Test\\\TestValidator@anonymous/');

        $validator = (new ValidatorBuilder())
            ->validator()
            ->get();

        $validator::type();
    }
}
