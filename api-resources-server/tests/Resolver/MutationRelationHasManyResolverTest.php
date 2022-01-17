<?php

namespace Afeefa\ApiResources\Tests\Resolver;

use Afeefa\ApiResources\Exception\Exceptions\InvalidConfigurationException;
use Afeefa\ApiResources\Exception\Exceptions\MissingCallbackException;
use Afeefa\ApiResources\Field\FieldBag;
use Afeefa\ApiResources\Field\Fields\VarcharAttribute;
use Afeefa\ApiResources\Field\Relation;
use Afeefa\ApiResources\Model\Model;
use Afeefa\ApiResources\Model\ModelInterface;
use Afeefa\ApiResources\Resolver\MutationRelationHasManyResolver;
use Afeefa\ApiResources\Test\MutationRelationTest;

use function Afeefa\ApiResources\Test\T;

use Afeefa\ApiResources\Type\Type;
use stdClass;

class MutationRelationHasManyResolverTest extends MutationRelationTest
{
    /**
     * @dataProvider missingCallbacksDataProvider
     */
    public function test_missing_callbacks($missingCallback)
    {
        $this->expectException(MissingCallbackException::class);
        $n = in_array($missingCallback, ['add', 'update']) ? 'n' : '';
        $this->expectExceptionMessage("Resolver for relation other needs to implement a{$n} {$missingCallback}() method.");

        $api = $this->createApiWithType(
            function (FieldBag $fields) use ($missingCallback) {
                $fields
                    ->relation('other', Type::list(T('TYPE')), function (Relation $relation) use ($missingCallback) {
                        $relation->resolveSave(function (MutationRelationHasManyResolver $r) use ($missingCallback) {
                            if ($missingCallback !== 'get') {
                                $r->get(fn () => []);
                            }
                            if ($missingCallback !== 'add') {
                                $r->add(fn () => null);
                            }
                            if ($missingCallback !== 'update') {
                                $r->update(fn () => null);
                            }
                            if ($missingCallback !== 'delete') {
                                $r->delete(fn () => null);
                            }
                        });
                    });
            }
        );

        $this->request($api, data: ['other' => []]);
    }

    public function missingCallbacksDataProvider()
    {
        return [
            ['get'],
            ['add'],
            ['update'],
            ['delete']
        ];
    }

    public function test_with_all_callbacks()
    {
        $api = $this->createApiWithType(
            function (FieldBag $fields) {
                $fields
                    ->relation('other', Type::list(T('TYPE')), function (Relation $relation) {
                        $relation->resolveSave(function (MutationRelationHasManyResolver $r) {
                            $r
                                ->get(fn () => [])
                                ->add(fn () => null)
                                ->update(fn () => null)
                                ->delete(fn () => null);
                        });
                    });
            }
        );

        $this->request($api, data: ['other' => []]);

        $this->assertTrue(true);
    }

    /**
     * @dataProvider createOwnerDataProvider
     */
    public function test_create_owner($data, $expectedInfo, $expectedInfo2, $expectedSaveFields)
    {
        $api = $this->createApiWithType(
            function (FieldBag $fields) {
                $fields
                    ->attribute('name', VarcharAttribute::class)
                    ->relation('other', Type::list(T('TYPE')), function (Relation $relation) {
                        $relation->resolveSave(function (MutationRelationHasManyResolver $r) {
                            $r
                                ->get(function () {
                                    $this->testWatcher->info('get');
                                })
                                ->add(function (ModelInterface $owner, string $typeName, array $saveFields) use ($r) {
                                    $this->testWatcher->info('add');

                                    $this->testWatcher->info2([
                                        $owner->apiResourcesGetId(),
                                        $owner->apiResourcesGetType(),
                                        $typeName,
                                        $r->getRelation()->getName()
                                    ]);

                                    $this->testWatcher->saveFields($saveFields);

                                    return Model::fromSingle('TYPE');
                                })
                                ->update(function () {
                                    $this->testWatcher->info('update');
                                })
                                ->delete(function () {
                                    $this->testWatcher->info('delete');
                                });
                        });
                    });
            }
        );

        $this->request($api, data: ['other' => $data]);

        $this->assertEquals($expectedInfo, $this->testWatcher->info);
        $this->assertEquals($expectedInfo2, $this->testWatcher->info2);
        $this->assertEquals($expectedSaveFields, $this->testWatcher->saveFields);
    }

    public function createOwnerDataProvider()
    {
        // $data, $expectedInfo, $expectedInfo2, $expectedSaveFields
        return [
            'new_empty' => [
                [],
                [],
                [],
                []
            ],

            'new_unknown_field' => [
                [['a' => 'b']],
                ['add'],
                [['111333', 'TYPE', 'TYPE', 'other']],
                [[]]
            ],

            'new_valid_field_no_id' => [
                [['name' => 'name1']],
                ['add'],
                [['111333', 'TYPE', 'TYPE', 'other']],
                [['name' => 'name1']]
            ],

            'new_valid_field_with_id' => [
                [['id' => '4', 'name' => 'name1']],
                ['add'],
                [['111333', 'TYPE', 'TYPE', 'other']],
                [['name' => 'name1']]
            ],
        ];
    }

    private $test_update_owner_existingData = [];

    /**
     * @dataProvider updateOwnerDataProvider
     */
    public function test_update_owner($existingData, $data, $expectedInfo, $expectedInfo2, $expectedSaveFields)
    {
        $this->test_update_owner_existingData = $existingData;

        $api = $this->createApiWithType(
            function (FieldBag $fields) {
                $fields
                    ->attribute('name', VarcharAttribute::class)
                    ->relation('other', Type::list(T('TYPE')), function (Relation $relation) {
                        $relation->resolveSave(function (MutationRelationHasManyResolver $r) {
                            $r
                                ->get(function () {
                                    $this->testWatcher->info('get');
                                    if ($this->test_update_owner_existingData) {
                                        return Model::fromList('TYPE', $this->test_update_owner_existingData);
                                    }
                                    return [];
                                })
                                ->add(function (ModelInterface $owner, string $typeName, array $saveFields) use ($r) {
                                    $this->testWatcher->info('add');

                                    $this->testWatcher->info2([
                                        $owner->apiResourcesGetId(),
                                        $owner->apiResourcesGetType(),
                                        $typeName,
                                        $r->getRelation()->getName()
                                    ]);

                                    $this->testWatcher->saveFields($saveFields);

                                    return Model::fromSingle('TYPE');
                                })
                                ->update(function (ModelInterface $owner, ModelInterface $modelToUpdate, array $saveFields) use ($r) {
                                    $this->testWatcher->info('update');

                                    $this->testWatcher->info2([
                                        $owner->apiResourcesGetId(),
                                        $owner->apiResourcesGetType(),
                                        $modelToUpdate->apiResourcesGetId(),
                                        $modelToUpdate->apiResourcesGetType(),
                                        $r->getRelation()->getName()
                                    ]);

                                    $this->testWatcher->saveFields($saveFields);
                                })
                                ->delete(function (ModelInterface $owner, ModelInterface $modelToDelete) use ($r) {
                                    $this->testWatcher->info('delete');

                                    $this->testWatcher->info2([
                                        $owner->apiResourcesGetId(),
                                        $owner->apiResourcesGetType(),
                                        $modelToDelete->apiResourcesGetId(),
                                        $modelToDelete->apiResourcesGetType(),
                                        $r->getRelation()->getName()
                                    ]);
                                });
                        });
                    });
            }
        );

        $this->request($api, data: ['other' => $data], params: ['id' => '111333']);

        $this->assertEquals($expectedInfo, $this->testWatcher->info);
        $this->assertEquals($expectedInfo2, $this->testWatcher->info2);
        $this->assertEquals($expectedSaveFields, $this->testWatcher->saveFields);
    }

    public function updateOwnerDataProvider()
    {
        // $existingData, $data, $expectedInfo, $expectedInfo2, $expectedSaveFields
        return [
            'new_empty' => [
                [],
                [],
                ['get'],
                [],
                []
            ],

            'new_unknown_field' => [
                [],
                [['a' => 'b']],
                ['get', 'add'],
                [['111333', 'TYPE', 'TYPE', 'other']],
                [[]]
            ],

            'new_valid_field_no_id' => [
                [],
                [['name' => 'name1']],
                ['get', 'add'],
                [['111333', 'TYPE', 'TYPE', 'other']],
                [['name' => 'name1']]
            ],

            'new_valid_field_with_id' => [
                [],
                [['id' => '4', 'name' => 'name1']],
                ['get', 'add'],
                [['111333', 'TYPE', 'TYPE', 'other']],
                [['name' => 'name1']]
            ],

            'existing_empty' => [
                [['id' => '10'], ['id' => '11']],
                [],
                ['get', 'delete', 'delete'],
                [['111333', 'TYPE', '10', 'TYPE', 'other'], ['111333', 'TYPE', '11', 'TYPE', 'other']],
                []
            ],

            'existing_unknown_field' => [
                [['id' => '10'], ['id' => '11']],
                [['a' => 'b', 'name' => 'nameb'], ['id' => '11', 'name' => 'name11']],
                ['get', 'delete', 'add', 'update'],
                [
                    ['111333', 'TYPE', '10', 'TYPE', 'other'],
                    ['111333', 'TYPE', 'TYPE', 'other'],
                    ['111333', 'TYPE', '11', 'TYPE', 'other']
                ],
                [
                    ['name' => 'nameb'],
                    ['name' => 'name11']
                ]
            ],

            'delete_not_present' => [
                [['id' => '10'], ['id' => '11']],
                [['id' => '11', 'name' => 'name11']],
                ['get', 'delete', 'update'],
                [['111333', 'TYPE', '10', 'TYPE', 'other'], ['111333', 'TYPE', '11', 'TYPE', 'other']],
                [['name' => 'name11']]
            ],

            'delete_add' => [
                [['id' => '10']],
                [['id' => '4', 'name' => 'name4']],
                ['get', 'delete', 'add'],
                [['111333', 'TYPE', '10', 'TYPE', 'other'], ['111333', 'TYPE', 'TYPE', 'other']],
                [['name' => 'name4']]
            ],

            'keep' => [
                [['id' => '4'], ['id' => '5']],
                [['id' => '4', 'name' => 'name4'], ['id' => '5', 'name' => 'name5']],
                ['get', 'update', 'update'],
                [['111333', 'TYPE', '4', 'TYPE', 'other'], ['111333', 'TYPE', '5', 'TYPE', 'other']],
                [
                    ['name' => 'name4'],
                    ['name' => 'name5']
                ]
            ],

            'keep_delete_add' => [
                [['id' => '4'], ['id' => '5'], ['id' => '6'], ['id' => '7']],
                [
                    ['id' => '4', 'name' => 'name4'],
                    ['id' => '5', 'name' => 'name5'],
                    ['id' => '8', 'name' => 'name8'],
                    ['id' => '9', 'name' => 'name9']
                ],
                ['get', 'delete', 'delete', 'update', 'update', 'add', 'add'],
                [
                    ['111333', 'TYPE', '6', 'TYPE', 'other'], ['111333', 'TYPE', '7', 'TYPE', 'other'],
                    ['111333', 'TYPE', '4', 'TYPE', 'other'], ['111333', 'TYPE', '5', 'TYPE', 'other'],
                    ['111333', 'TYPE', 'TYPE', 'other'], ['111333', 'TYPE', 'TYPE', 'other']
                ],
                [
                    ['name' => 'name4'],
                    ['name' => 'name5'],
                    ['name' => 'name8'],
                    ['name' => 'name9']
                ]
            ]
        ];
    }

    /**
     * @dataProvider getDoesNotReturnModelsDataProvider
     */
    public function test_get_does_not_return_array_of_models($return)
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Get callback of resolver for relation other must return an array of ModelInterface objects.');

        $api = $this->createApiWithType(
            function (FieldBag $fields) use ($return) {
                $fields
                    ->relation('other', T('TYPE'), function (Relation $relation) use ($return) {
                        $relation->resolveSave(function (MutationRelationHasManyResolver $r) use ($return) {
                            $r
                                ->get(function () use ($return) {
                                    if ($return !== 'NOTHING') {
                                        return $return;
                                    }
                                })
                                ->add(fn () => TestModel::fromSingle('TYPE'))
                                ->update(fn () => null)
                                ->delete(fn () => null);
                        });
                    });
            }
        );

        $this->request($api, data: ['other' => []], params: ['id' => '111333']);

        $this->assertTrue(true);
    }

    public function getDoesNotReturnModelsDataProvider()
    {
        return [
            'null' => [null],
            'array_of_null' => [[null, null]],
            'string' => ['string'],
            'array_of_strings' => [['string', 'string']],
            'object' => [new stdClass()],
            'array_of_objects' => [[new stdClass(), new stdClass()]],
            'nothing' => ['NOTHING']
        ];
    }

    /**
     * @dataProvider addDoesNotReturnModelDataProvider
     */
    public function test_add_does_not_return_model_or_null($updateOwner, $return)
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Add callback of resolver for relation other must return a ModelInterface object.');

        $api = $this->createApiWithType(
            function (FieldBag $fields) use ($return) {
                $fields
                    ->relation('other', T('TYPE'), function (Relation $relation) use ($return) {
                        $relation->resolveSave(function (MutationRelationHasManyResolver $r) use ($return) {
                            $r
                                ->get(fn () => [])
                                ->add(function () use ($return) {
                                    if ($return !== 'NOTHING') {
                                        return $return;
                                    }
                                })
                                ->update(fn () => null)
                                ->delete(fn () => null);
                        });
                    });
            }
        );

        $params = $updateOwner ? ['id' => '123'] : [];
        $this->request($api, data: ['other' => [[]]], params: $params);

        $this->assertTrue(true);
    }

    public function addDoesNotReturnModelDataProvider()
    {
        return [
            'create_null' => [false, null],
            'create_array' => [false, []],
            'create_string' => [false, 'string'],
            'create_object' => [false, new stdClass()],
            'create_nothing' => [false, 'NOTHING'],
            'update_null' => [true, null],
            'update_array' => [true, []],
            'update_string' => [true, 'string'],
            'update_object' => [true, new stdClass()],
            'update_nothing' => [true, 'NOTHING']
        ];
    }
}
