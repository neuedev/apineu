<?php

namespace Neuedev\Apineu\Tests\Api\Schema;

use Closure;
use Neuedev\Apineu\Action\Action;
use Neuedev\Apineu\Api\ApiRequest;
use Neuedev\Apineu\Model\Model;
use Neuedev\Apineu\Resolver\MutationActionResolver;
use Neuedev\Apineu\Resolver\QueryActionResolver;
use Neuedev\Apineu\Test\ApiResourcesTest;

use function Neuedev\Apineu\Test\T;

class RequestActionTest extends ApiResourcesTest
{
    public function test_query()
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
    }

    public function test_mutation_returns_null()
    {
        $api = $this->apiBuilder()->api('API', function (Closure $addResource) {
            $addResource('RES', function (Closure $addAction, Closure $addQuery, Closure $addMutation) {
                $addMutation('ACT', T('TYPE'), function (Action $action) {
                    $action
                        ->resolve(function (MutationActionResolver $r) {
                            $r->save(fn () => null);
                        });
                });
            });
        })->get();

        $result = $api->request(function (ApiRequest $request) {
            $request
                ->resourceType('RES')
                ->actionName('ACT')
                ->fields([
                    'name' => true
                ])
                ->fieldsToSave([
                    'name' => 'jens'
                ]);
        });

        $this->assertNull($result['data']);
    }
}
