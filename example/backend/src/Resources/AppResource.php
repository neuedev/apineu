<?php

namespace Backend\Resources;

use Backend\Types\CountsType;
use Closure;
use Medoo\Medoo;
use Neuedev\Apineu\Action\Action;
use Neuedev\Apineu\Action\ActionBag;
use Neuedev\Apineu\Api\ApiRequest;
use Neuedev\Apineu\Model\Model;
use Neuedev\Apineu\Resolver\QueryActionResolver;
use Neuedev\Apineu\Resource\Resource;

class AppResource extends Resource
{
    protected static string $type = 'Example.AppResource';

    protected function actions(ActionBag $actions): void
    {
        $actions->query('get_counts', CountsType::class, function (Action $action) {
            $action
                ->resolve(function (QueryActionResolver $r, Medoo $db) {
                    $r->get(function (ApiRequest $request, Closure $getSelectFields) use ($db) {
                        $selectFields = $getSelectFields();

                        $attributes = ['id' => 'app'];

                        if (in_array('count_articles', $selectFields)) {
                            $attributes['count_articles'] = $db->count('articles');
                        }

                        if (in_array('count_authors', $selectFields)) {
                            $attributes['count_authors'] = $db->count('authors');
                        }

                        return Model::fromSingle('Example.Counts', $attributes);
                    });
                });
        });
    }
}
