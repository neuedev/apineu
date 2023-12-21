<?php

namespace Neuedev\Apineu\Tests\Api\Schema;

use Closure;
use Neuedev\Apineu\Action\Action;
use Neuedev\Apineu\Exception\Exceptions\MissingTypeException;
use Neuedev\Apineu\Test\ApiBuilder;

use Neuedev\Apineu\Test\ApiResourcesTest;

use function Neuedev\Apineu\Test\createApiWithSingleResource;
use Neuedev\Apineu\Test\ResourceBuilder;

use function Neuedev\Apineu\Test\T;

class SchemaResourceTest extends ApiResourcesTest
{
    public function test_simple()
    {
        $api = createApiWithSingleResource(function (Closure $addAction, Closure $addQuery) {
            $addQuery('test_action', T('Test.Type'), function (Action $action) {
                $action
                    ->resolve(function () {
                    });
            });
            $addQuery('test_action2', T('Test.Type2'), function (Action $action) {
                $action
                    ->resolve(function () {
                    });
            });
        });

        $schema = $api->toSchemaJson();

        $expectedResourcesSchema = [
            'Test.Resource' => [
                'test_action' => [
                    'response' => [
                        'type' => 'Test.Type'
                    ]
                ],
                'test_action2' => [
                    'response' => [
                        'type' => 'Test.Type2'
                    ]
                ]
            ]
        ];

        $this->assertEquals($expectedResourcesSchema, $schema['resources']);
    }

    public function test_get_type_with_missing_type()
    {
        $this->expectException(MissingTypeException::class);
        $this->expectExceptionMessageMatches('/^Missing type for class Neuedev\\\Apineu\\\Test\\\TestResource@anonymous/');

        $resource = (new ResourceBuilder())->resource()->get();

        $resource::type();
    }

    public function test_add_with_missing_type()
    {
        $this->expectException(MissingTypeException::class);
        $this->expectExceptionMessageMatches('/^Missing type for class Neuedev\\\Apineu\\\Test\\\TestResource@anonymous/');

        (new ApiBuilder())
            ->api('Test.Api', function (Closure $addResource) {
                $addResource();
            })
            ->get();
    }
}
