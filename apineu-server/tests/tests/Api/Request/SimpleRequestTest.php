<?php

namespace Neuedev\Apineu\Tests\Api\Schema;

use Closure;
use Neuedev\Apineu\Action\Action;
use Neuedev\Apineu\Api\ApiRequest;
use Neuedev\Apineu\Field\FieldBag;
use Neuedev\Apineu\Field\Fields\StringAttribute;
use Neuedev\Apineu\Model\Model;
use Neuedev\Apineu\Resolver\QueryActionResolver;
use Neuedev\Apineu\Test\ApiResourcesTest;

use function Neuedev\Apineu\Test\T;

class SimpleRequestTest extends ApiResourcesTest
{
    public function test_request()
    {
        $api = $this->apiBuilder()->api('API', function (Closure $addResource) {
            $addResource('RES', function (Closure $addAction, Closure $addQuery) {
                $addQuery('ACT', T('TYPE'), function (Action $action) {
                    $action
                        ->resolve(function (QueryActionResolver $resolver) {
                            $resolver->get(function () {
                                return Model::fromSingle('TYPE', [
                                    'id' => '123',
                                    'name' => 'test'
                                ]);
                            });
                        });
                });
            });
        })->get();

        // request via php interface

        $result = $api->request(function (ApiRequest $request) {
            $request
                ->resourceType('RES')
                ->actionName('ACT')
                ->fields([
                    'name' => true
                ]);
        });

        $data = ($result['data'])->jsonSerialize();

        $expectedData = [
            'type' => 'TYPE',
            'id' => '123'
        ];

        $this->assertEquals($expectedData, $data);

        // request via input

        $result = $api->request(function (ApiRequest $request) {
            $request
                ->fromInput([
                    'resource' => 'RES',
                    'action' => 'ACT',
                    'fields' => [
                        'name' => true
                    ]
                ]);
        });

        $data = ($result['data'])->jsonSerialize();

        $expectedData = [
            'type' => 'TYPE',
            'id' => '123'
        ];

        $this->assertEquals($expectedData, $data);
    }

    public function test_request_with_attribute()
    {
        $api = $this->apiBuilder()->api('API', function (Closure $addResource, Closure $addType) {
            $addType('TYPE', function (FieldBag $fields) {
                $fields->attribute('name', StringAttribute::class);
            });

            $addResource('RES', function (Closure $addAction, Closure $addQuery) {
                $addQuery('ACT', T('TYPE'), function (Action $action) {
                    $action
                        ->resolve(function (QueryActionResolver $resolver) {
                            $resolver->get(function () {
                                return Model::fromSingle('TYPE', [
                                    'id' => '123',
                                    'name' => 'test'
                                ]);
                            });
                        });
                });
            });
        })->get();

        // request via php interface

        $result = $api->request(function (ApiRequest $request) {
            $request
                ->resourceType('RES')
                ->actionName('ACT')
                ->fields(['name' => true]);
        });

        $data = ($result['data'])->jsonSerialize();

        $expectedData = [
            'type' => 'TYPE',
            'id' => '123',
            'name' => 'test'
        ];

        $this->assertEquals($expectedData, $data);

        // request via input

        $result = $api->request(function (ApiRequest $request) {
            $request
                ->fromInput([
                    'resource' => 'RES',
                    'action' => 'ACT',
                    'fields' => [
                        'name' => true
                    ]
                ]);
        });

        $data = ($result['data'])->jsonSerialize();

        $expectedData = [
            'type' => 'TYPE',
            'id' => '123',
            'name' => 'test'
        ];

        $this->assertEquals($expectedData, $data);
    }
}
