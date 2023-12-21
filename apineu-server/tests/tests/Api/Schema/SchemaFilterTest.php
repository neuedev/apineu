<?php

namespace Neuedev\Apineu\Tests\Api\Schema;

use Closure;
use Neuedev\Apineu\Action\Action;
use Neuedev\Apineu\Api\ApiRequest;
use Neuedev\Apineu\Exception\Exceptions\MissingTypeException;
use Neuedev\Apineu\Field\FieldBag;
use Neuedev\Apineu\Field\Fields\StringAttribute;
use Neuedev\Apineu\Filter\Filter;
use Neuedev\Apineu\Filter\FilterBag;

use Neuedev\Apineu\Test\ApiResourcesTest;
use function Neuedev\Apineu\Test\createApiWithSingleResource;
use Neuedev\Apineu\Test\FilterBuilder;

use function Neuedev\Apineu\Test\T;

class SchemaFilterTest extends ApiResourcesTest
{
    public function test_simple()
    {
        $api = $this->createApiWithFilter('check', function (Filter $filter) {
            $filter
                ->options([true, false])
                ->default('default');
        });

        $schema = $api->toSchemaJson();

        $expectedResourcesSchema = [
            'Test.Resource' => [
                'test_action' => [
                    'filters' => [
                        'check' => [
                            'type' => 'Test.Filter',
                            'options' => [true, false],
                            'default' => 'default'
                        ]
                    ],
                    'response' => [
                        'type' => 'Test.Type'
                    ]
                ]
            ]
        ];

        $this->assertEquals($expectedResourcesSchema, $schema['resources']);
    }

    public function test_implicitly_null_is_option()
    {
        $api = $this->createApiWithFilter('check', function (Filter $filter) {
            $filter
                ->options([null, true, false]);
        });

        $schema = $api->toSchemaJson();

        $expectedResourcesSchema = [
            'Test.Resource' => [
                'test_action' => [
                    'filters' => [
                        'check' => [
                            'type' => 'Test.Filter',
                            'options' => [null, true, false]
                        ]
                    ],
                    'response' => [
                        'type' => 'Test.Type'
                    ]
                ]
            ]
        ];

        $this->assertEquals($expectedResourcesSchema, $schema['resources']);
    }

    public function test_options_request()
    {
        // auto save type with field 'name' into registry
        $this->typeBuilder()->type('Test.Type', function (FieldBag $fields) {
            $fields->attribute('name', StringAttribute::class);
        })->get();

        $api = $this->createApiWithFilter('check', function (Filter $filter) {
            $filter
                ->optionsRequest(function (ApiRequest $request) {
                    $request
                        ->resourceType('Test.Resource')
                        ->actionName('test_action')
                        ->fields(['name' => true]);
                });
        });

        $schema = $api->toSchemaJson();

        $expectedResourcesSchema = [
            'Test.Resource' => [
                'test_action' => [
                    'filters' => [
                        'check' => [
                            'type' => 'Test.Filter',
                            'options_request' => [
                                'api' => 'Test.Api',
                                'resource' => 'Test.Resource',
                                'action' => 'test_action',
                                'fields' => [
                                    'name' => true
                                ]
                            ]
                        ]
                    ],
                    'response' => [
                        'type' => 'Test.Type'
                    ]
                ]
            ]
        ];

        $this->assertEquals($expectedResourcesSchema, $schema['resources']);
    }

    public function test_add_with_missing_type()
    {
        $this->expectException(MissingTypeException::class);
        $this->expectExceptionMessageMatches('/^Missing type for class Neuedev\\\Apineu\\\Test\\\TestFilter@anonymous/');

        $filter = (new FilterBuilder())->filter()->get();

        $api = createApiWithSingleResource(function (Closure $addAction, Closure $addQuery) use ($filter) {
            $addQuery('test_action', T('Test.Type'), function (Action $action) use ($filter) {
                $action
                    ->filters(function (FilterBag $filters) use ($filter) {
                        $filters->add('test_filter', $filter::class);
                    })
                    ->resolve(function () {
                    });
            });
        });

        $api->toSchemaJson();
    }

    private function createApiWithFilter($name, Closure $filterCallback)
    {
        $filter = (new FilterBuilder())->filter('Test.Filter')->get();

        return createApiWithSingleResource(function (Closure $addAction, Closure $addQuery) use ($name, $filter, $filterCallback) {
            $addQuery('test_action', T('Test.Type'), function (Action $action) use ($name, $filter, $filterCallback) {
                $action
                    ->filters(function (FilterBag $filters) use ($name, $filter, $filterCallback) {
                        $filters->add($name, $filter::class);
                        $filterCallback($filters->get($name));
                    })
                    ->resolve(function () {
                    });
            });
        });
    }
}
