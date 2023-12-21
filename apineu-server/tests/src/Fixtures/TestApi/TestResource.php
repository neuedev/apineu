<?php

namespace Neuedev\Apineu\Test\Fixtures\TestApi;

use Neuedev\Apineu\Action\Action;
use Neuedev\Apineu\Action\ActionBag;
use Neuedev\Apineu\Filter\FilterBag;
use Neuedev\Apineu\Filter\Filters\PageSizeFilter;
use Neuedev\Apineu\Resource\Resource;
use Neuedev\Apineu\Type\Type;

class TestResource extends Resource
{
    protected static string $type = 'TestResource';

    protected function actions(ActionBag $actions): void
    {
        $actions->query('get_types', Type::list(TestType::class), function (Action $action) {
            $action
                ->filters(function (FilterBag $filters) {
                    $filters->add('page_size', function (PageSizeFilter $filter) {
                        $filter
                            ->pageSizes([5, 15, 30, 50])
                            ->default(5);
                    });
                })

                ->resolve([TestResolver::class, 'get_types']);
        });
    }
}
