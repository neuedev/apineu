<?php

namespace Neuedev\Apineu\Resolver\Action;

use Neuedev\Apineu\Api\ApiRequest;
use Neuedev\Apineu\Resolver\Base\BaseResolver;

class BaseActionResolver extends BaseResolver
{
    protected ApiRequest $request;

    public function request(ApiRequest $request): BaseActionResolver
    {
        $this->request = $request;
        return $this;
    }

    public function getRequest(): ApiRequest
    {
        return $this->request;
    }

    public function resolve(): array
    {
        return [];
    }
}
