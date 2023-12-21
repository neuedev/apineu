<?php

namespace Neuedev\Apineu\Tests\Field;

use Closure;
use Error;
use Neuedev\Apineu\Action\Action;
use Neuedev\Apineu\Api\ApiRequest;
use Neuedev\Apineu\Exception\Exceptions\MissingTypeException;
use Neuedev\Apineu\Test\ApiResourcesTest;
use function Neuedev\Apineu\Test\createApiWithSingleType;
use Neuedev\Apineu\Test\FieldBuilder;
use function Neuedev\Apineu\Test\T;

class FieldTest extends ApiResourcesTest
{
    public function test_defaults()
    {
        $field = (new FieldBuilder())->field('Field')->get()
            ->name('title');

        $this->assertEquals('Field', $field::type());
        $this->assertEquals('title', $field->getName());
        $this->assertFalse($field->isRequired());
        $this->assertFalse($field->hasOptions());
        $this->assertSame([], $field->getOptions());
        $this->assertFalse($field->hasOptionsRequest());
        $this->assertNull($field->getOptionsRequest());
        $this->assertFalse($field->hasResolver());
        $this->assertFalse($field->hasResolver());
        $this->assertFalse($field->hasResolver());
        $this->assertFalse($field->hasResolveParam('something'));
    }

    public function test_options()
    {
        $field = (new FieldBuilder())->field('Field')->get()
            ->name('title')
            ->options(['great', 'cool']);

        $this->assertSame(['great', 'cool'], $field->getOptions());
    }

    public function test_options_request()
    {
        createApiWithSingleType();

        $field = $this->fieldBuilder()->field('Field')->get()
            ->optionsRequest(function (ApiRequest $request) {
                $request
                    ->resourceType('Test.Resource')
                    ->actionName('test_action')
                    ->fields(['name' => true]);
            });

        $request = $field->getOptionsRequest();

        $this->assertInstanceOf(ApiRequest::class, $request);

        $this->assertEquals('Test.Api', $request->getApi()::type());
        $this->assertEquals('Test.Resource', $request->getResource()::type());
        $this->assertEquals('test_action', $request->getAction()->getName());
    }

    public function test_clone()
    {
        $originalField = (new FieldBuilder())->field('Field')->get()
            ->name('title');
        $field = $originalField->clone();

        $this->assertEquals('Field', $field::type());
        $this->assertEquals('title', $field->getName());
        $this->assertFalse($field->isRequired());
        $this->assertFalse($field->hasOptions());
        $this->assertSame([], $field->getOptions());
        $this->assertFalse($field->hasOptionsRequest());
        $this->assertNull($field->getOptionsRequest());
        $this->assertFalse($field->hasResolver());
        $this->assertFalse($field->hasResolver());
        $this->assertFalse($field->hasResolver());
        $this->assertFalse($field->hasResolveParam('something'));
    }

    public function test_clone_required()
    {
        $originalField = (new FieldBuilder())->field('Field')->get()
            ->name('title')
            ->required();
        $this->assertTrue($originalField->isRequired());

        $field = $originalField->clone();

        $this->assertTrue($field->isRequired());
    }

    public function test_clone_options()
    {
        $originalField = (new FieldBuilder())->field('Field')->get()
            ->name('title')
            ->options(['great', 'cool']);
        $this->assertSame(['great', 'cool'], $originalField->getOptions());

        $field = $originalField->clone();

        $this->assertSame(['great', 'cool'], $field->getOptions());

        $originalField->options(['bad', 'sad']);
        $this->assertSame(['bad', 'sad'], $originalField->getOptions());
        $this->assertSame(['great', 'cool'], $field->getOptions());
    }

    public function test_clone_options_request()
    {
        $this->apiBuilder()->api('API', function (Closure $addResource) {
            $addResource('RES', function (Closure $addAction, Closure $addQuery) {
                $addQuery('ACT', T('TYPE'), function (Action $action) {
                    $action
                        ->resolve(function () {
                        });
                });
            });
            $addResource('RES2', function (Closure $addAction, Closure $addQuery) {
                $addQuery('ACT2', T('TYPE'), function (Action $action) {
                    $action
                        ->resolve(function () {
                        });
                });
            });
        })->get();

        $originalField = $this->fieldBuilder()->field('FIELD')->get()
            ->name('test_field')
            ->optionsRequest(function (ApiRequest $request) {
                $request
                    ->resourceType('RES')
                    ->actionName('ACT');
            });

        // original
        $originalRequest = $originalField->getOptionsRequest();
        $this->assertEquals('API', $originalRequest->getApi()::type());
        $this->assertEquals('RES', $originalRequest->getResource()::type());
        $this->assertEquals('ACT', $originalRequest->getAction()->getName());

        // clone
        $field = $originalField->clone();

        // request same as original
        $request = $field->getOptionsRequest();
        $this->assertInstanceOf(ApiRequest::class, $request);
        $this->assertEquals('API', $request->getApi()::type());
        $this->assertEquals('RES', $request->getResource()::type());
        $this->assertEquals('ACT', $request->getAction()->getName());

        // update original
        $originalField->optionsRequest(function (ApiRequest $request) {
            $request
                ->resourceType('RES2')
                ->actionName('ACT2');
        });
        $originalRequest = $originalField->getOptionsRequest();
        $this->assertEquals('API', $originalRequest->getApi()::type());
        $this->assertEquals('RES2', $originalRequest->getResource()::type());
        $this->assertEquals('ACT2', $originalRequest->getAction()->getName());

        // request not changed after original update
        $request = $field->getOptionsRequest();
        $this->assertEquals('API', $request->getApi()::type());
        $this->assertEquals('RES', $request->getResource()::type());
        $this->assertEquals('ACT', $request->getAction()->getName());

        // clone again
        $field = $originalField->clone();

        // request again same as original
        $request = $field->getOptionsRequest();
        $this->assertInstanceOf(ApiRequest::class, $request);
        $this->assertEquals('API', $request->getApi()::type());
        $this->assertEquals('RES2', $request->getResource()::type());
        $this->assertEquals('ACT2', $request->getAction()->getName());
    }

    public function test_get_type_with_missing_type()
    {
        $this->expectException(MissingTypeException::class);
        $this->expectExceptionMessageMatches('/^Missing type for class Neuedev\\\Apineu\\\Test\\\TestField@anonymous/');

        $field = (new FieldBuilder())->field()->get();

        $field->type();
    }

    public function test_get_name_missing_name()
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('Typed property Neuedev\Apineu\Field\Field::$name must not be accessed before initialization');

        $field = (new FieldBuilder())->field()->get();

        $field->getName();
    }
}
