<?php

namespace Backend\Resources;

use Backend\Models\Author;
use Backend\Models\Comment;
use Neuedev\Apineu\Action\Action;
use Neuedev\Apineu\Action\ActionBag;
use Neuedev\Apineu\Filter\FilterBag;
use Neuedev\Apineu\Filter\Filters\KeywordFilter;
use Neuedev\Apineu\Resource\Resource;
use Neuedev\Apineu\Type\Type;

class SearchResource extends Resource
{
    protected static string $type = 'Example.SearchResource';

    protected function actions(ActionBag $actions): void
    {
        $actions->query(
            'search',
            Type::list([Article::class, Author::class, Comment::class]),
            function (Action $action) {
                $action->filters(function (FilterBag $filters) {
                    $filters->add('q', KeywordFilter::class);
                });
            }
        );
    }
}
