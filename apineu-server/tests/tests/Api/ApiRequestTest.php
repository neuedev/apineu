<?php

namespace Neuedev\Apineu\Tests\Api;

use Closure;
use Neuedev\Apineu\Action\Action;
use Neuedev\Apineu\Api\Api;
use Neuedev\Apineu\Api\ApiRequest;
use Neuedev\Apineu\DI\Container;
use Neuedev\Apineu\Model\Model;
use Neuedev\Apineu\Resolver\MutationActionResolver;
use Neuedev\Apineu\Test\ApiResourcesTest;
use Neuedev\Apineu\Test\Fixtures\TestApi\TestApi;
use Neuedev\Apineu\Test\Fixtures\TestApi\TestResource;
use function Neuedev\Apineu\Test\T;
use Neuedev\Apineu\Type\Type;

use Neuedev\Apineu\Validator\ValidationFailedException;

class ApiRequestTest extends ApiResourcesTest
{
    public function test_request()
    {
        $models = $this->request(5, [
            'attr1' => true
        ]);

        $this->assertCount(5, $models);

        $this->assertFields($models, ['attr1']);
    }

    public function test_request_multiple_attributes()
    {
        $models = $this->request(5, [
            'attr1' => true,
            'attr2' => true,
            'attr3' => true
        ]);

        $this->assertCount(5, $models);

        $this->assertFields($models, ['attr1', 'attr2', 'attr3']);
    }

    public function test_request_wrong_attributes()
    {
        $models = $this->request(5, [
            'attr4' => true
        ]);

        $this->assertCount(5, $models);

        $this->assertFields($models);

        $models = $this->request(5, [
            'attr1' => true,
            'attr4' => true
        ]);

        $this->assertCount(5, $models);

        $this->assertFields($models, ['attr1']);
    }

    public function test_request_no_attributes()
    {
        $models = $this->request(5);

        $this->assertCount(5, $models);

        $this->assertFields($models);
    }

    public function test_request_filter()
    {
        $models = $this->request(15, [
            'attr1' => true
        ]);

        $this->assertCount(15, $models);

        $this->assertFields($models, ['attr1']);
    }

    public function test_request_no_filter()
    {
        $models = $this->request(null, [
            'attr1' => true
        ]);

        $this->assertCount(5, $models);

        $this->assertFields($models, ['attr1']);
    }

    public function test_request_no_filter_no_fields()
    {
        $models = $this->request();

        $this->assertCount(5, $models);

        $this->assertFields($models);
    }

    /**
     * @dataProvider wrongValueSingleDataProvider
     */
    public function test_mutation_wrong_value_single($value)
    {
        $this->expectException(ValidationFailedException::class);
        $this->expectExceptionMessage('Data passed to the mutation action ACT on resource RES must be an array or null.');

        $api = $this->createApiWithMutation(
            fn () => T('TYPE'),
            function (Action $action) {
                $action
                    ->resolve(function ($r) {
                        $r->save(function () {
                        });
                    });
            }
        );

        $this->requestSave(
            $api,
            data: $value
        );
    }

    public function wrongValueSingleDataProvider()
    {
        return [
            'string' => ['wrong'],
            'number' => [1]
        ];
    }

    /**
     * @dataProvider wrongValueManyDataProvider
     */
    public function test_mutation_wrong_value_many($value)
    {
        $this->expectException(ValidationFailedException::class);
        $this->expectExceptionMessage('Data passed to the mutation action ACT on resource RES must be an array or null.');

        $api = $this->createApiWithMutation(
            fn () => Type::list(T('TYPE')),
            function (Action $action) {
                $action
                    ->resolve(function (MutationActionResolver $r) {
                        $r->save(function () {
                            return Model::fromSingle('TYPE');
                        });
                    });
            }
        );

        $this->requestSave(
            $api,
            data: $value
        );
    }

    public function wrongValueManyDataProvider()
    {
        return [
            'string' => ['wrong'],
            'number' => [1]
        ];
    }

    private function createApiWithAction($TypeClassOrClassesOrMeta, Closure $actionCallback): Api
    {
        return $this->apiBuilder()->api('API', function (Closure $addResource) use ($TypeClassOrClassesOrMeta, $actionCallback) {
            $addResource('RES', function (Closure $addAction, Closure $addQuery) use ($TypeClassOrClassesOrMeta, $actionCallback) {
                $addQuery('ACT', $TypeClassOrClassesOrMeta, $actionCallback);
            });
        })->get();
    }

    private function createApiWithMutation($TypeClassOrClassesOrMeta, Closure $actionCallback): Api
    {
        return $this->apiBuilder()->api('API', function (Closure $addResource) use ($TypeClassOrClassesOrMeta, $actionCallback) {
            $addResource('RES', function (Closure $addAction, Closure $addQuery, Closure $addMutation) use ($TypeClassOrClassesOrMeta, $actionCallback) {
                $addMutation('ACT', $TypeClassOrClassesOrMeta, $actionCallback);
            });
        })->get();
    }

    private function request(?int $count = null, array $fields = null)
    {
        $container = new Container();
        $api = $container->create(TestApi::class);

        $result = $api->request(function (ApiRequest $request) use ($count, $fields) {
            $request = $request
                ->resourceType(TestResource::type())
                ->actionName('get_types');

            if ($count !== null) {
                $request->filters([
                    'page_size' => $count
                ]);
            }

            if ($fields) {
                $request->fields($fields);
            }

            return $request;
        });

        return $this->toJson($result['data']);
    }

    private function requestSave(Api $api, $data = 'unset'): array
    {
        return $api->request(function (ApiRequest $request) use ($data) {
            $request
                ->resourceType('RES')
                ->actionName('ACT');

            if ($data !== 'unset') {
                $request->fieldsToSave($data);
            }
        });
    }

    private function assertFields(array $models, array $fieldNames = [])
    {
        foreach ($models as $index => $model) {
            $this->assertCount(count($fieldNames) + 2, array_keys($model));
            $this->assertEquals($index + 1, $model['id']);
            $this->assertEquals('TestType', $model['type']);
            foreach ($fieldNames as $fieldName) {
                $this->assertTrue($model[$fieldName]);
            }
        }
    }

    private function toJson(array $data)
    {
        return array_map(function (Model $model) {
            return $model->jsonSerialize();
        }, $data);
    }
}
