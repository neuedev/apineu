<?php

namespace Backend\Resources;

use Backend\Resolvers\AuthorsResolver;
use Backend\Types\AuthorType;
use Neuedev\Apineu\Action\Action;
use Neuedev\Apineu\Action\ActionBag;
use Neuedev\Apineu\Action\ActionParams;
use Neuedev\Apineu\Api\ApiRequest;
use Neuedev\Apineu\Field\Fields\IdAttribute;
use Neuedev\Apineu\Filter\FilterBag;
use Neuedev\Apineu\Filter\Filters\KeywordFilter;
use Neuedev\Apineu\Filter\Filters\OrderFilter;
use Neuedev\Apineu\Filter\Filters\PageFilter;
use Neuedev\Apineu\Filter\Filters\PageSizeFilter;
use Neuedev\Apineu\Filter\Filters\SelectFilter;
use Neuedev\Apineu\Resource\Resource;
use Neuedev\Apineu\Type\Type;

class AuthorResource extends Resource
{
    protected static string $type = 'Example.AuthorResource';

    protected function actions(ActionBag $actions): void
    {
        $actions
            ->query('get_authors', Type::list(AuthorType::class), function (Action $action) {
                $action->filters(function (FilterBag $filters) {
                    $filters->add('q', KeywordFilter::class);

                    $filters->add('tag_id', function (SelectFilter $filter) {
                        $filter->optionsRequest(function (ApiRequest $request) {
                            $request
                                ->resourceType(TagResource::type())
                                ->actionName('get_tags')
                                ->fields(['name' => true, 'count_users' => true]);
                        });
                    });

                    $filters->add('order', function (OrderFilter $filter) {
                        $filter
                            ->fields([
                                'id' => [OrderFilter::DESC, OrderFilter::ASC],
                                'name' => [OrderFilter::ASC], OrderFilter::DESC,
                                'count_articles' => [OrderFilter::DESC, OrderFilter::ASC]
                            ])
                            ->default(['name' => OrderFilter::ASC]);
                    });

                    $filters->add('page_size', function (PageSizeFilter $filter) {
                        $filter
                            ->pageSizes([5, 10, 15])
                            ->default(10);
                    });

                    $filters->add('page', PageFilter::class);
                });

                $action->resolve([AuthorsResolver::class, 'get_authors']);
            })

            ->query('get_author', AuthorType::class, function (Action $action) {
                $action
                    ->params(function (ActionParams $params) {
                        $params->attribute('id', IdAttribute::class);
                    })

                    ->resolve([AuthorsResolver::class, 'get_author']);
            })

            ->mutation('save_author', AuthorType::class, function (Action $action) {
                $action
                    ->params(function (ActionParams $params) {
                        $params->attribute('id', IdAttribute::class);
                    })

                    ->response(AuthorType::class)

                    ->resolve([AuthorsResolver::class, 'save_author']);
            });
    }
}
