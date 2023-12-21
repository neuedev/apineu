<?php

use Neuedev\Apineu\Test\TestResource;

return new class () extends TestResource {
    protected static string $type = 'Test.Resource';

    public static ?Closure $addActionCallback;
};
