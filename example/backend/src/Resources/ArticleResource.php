<?php

namespace Backend\Resources;

use Backend\Resolvers\ArticlesResolver;
use Backend\Types\ArticleType;
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

class ArticleResource extends Resource
{
    protected static string $type = 'Example.ArticleResource';

    protected function actions(ActionBag $actions): void
    {
        $actions
            ->query('get_articles', Type::list(ArticleType::class), function (Action $action) {
                $action->params(function (ActionParams $params) {
                    $params->attribute('author_id', IdAttribute::class);
                });

                $action->filters(function (FilterBag $filters) {
                    $filters->add('author_id', function (SelectFilter $filter) {
                        $filter->optionsRequest(function (ApiRequest $request) {
                            $request
                                ->resourceType(AuthorResource::type())
                                ->actionName('get_authors')
                                ->fields(['name' => true, 'count_articles' => true]);
                        });
                    });

                    $filters->add('tag_id', function (SelectFilter $filter) {
                        $filter->optionsRequest(function (ApiRequest $request) {
                            $request
                                ->resourceType(TagResource::type())
                                ->actionName('get_tags')
                                ->fields(['name' => true, 'count_users' => true]);
                        });
                    });

                    $filters->add('q', KeywordFilter::class);

                    $filters->add('order', function (OrderFilter $filter) {
                        $filter
                            ->fields([
                                'id' => [OrderFilter::DESC, OrderFilter::ASC],
                                'title' => [OrderFilter::ASC, OrderFilter::DESC],
                                'date' => [OrderFilter::DESC, OrderFilter::ASC],
                                'count_comments' => [OrderFilter::DESC, OrderFilter::ASC],
                                'author_name' => [OrderFilter::ASC, OrderFilter::DESC]
                            ])
                            ->default(['date' => OrderFilter::DESC]);
                    });

                    $filters->add('page_size', function (PageSizeFilter $filter) {
                        $filter
                            ->pageSizes([15, 30, 50])
                            ->default(30);
                    });

                    $filters->add('page', PageFilter::class);
                });

                $action->resolve([ArticlesResolver::class, 'get_articles']);
            })

            ->query('get_article', ArticleType::class, function (Action $action) {
                $action
                    ->params(function (ActionParams $params) {
                        $params->attribute('id', IdAttribute::class);
                    })

                    ->resolve([ArticlesResolver::class, 'get_article']);
            })

            ->mutation('save_article', ArticleType::class, function (Action $action) {
                $action
                    ->params(function (ActionParams $params) {
                        $params->attribute('id', IdAttribute::class);
                    })

                    ->response(ArticleType::class)

                    ->resolve([ArticlesResolver::class, 'save_article']);
            });
    }
}
