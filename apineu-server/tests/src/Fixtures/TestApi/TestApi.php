<?php

namespace Neuedev\Apineu\Test\Fixtures\TestApi;

use Neuedev\Apineu\Api\Api;
use Neuedev\Apineu\Resource\ResourceBag;

class TestApi extends Api
{
    protected static string $type = 'TestApi';

    protected function resources(ResourceBag $resources): void
    {
        $resources
            ->add(TestResource::class);
    }
}
