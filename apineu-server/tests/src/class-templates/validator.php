<?php

use Neuedev\Apineu\Test\TestValidator;

return new class () extends TestValidator {
    protected static string $type = 'Test.Validator';

    public static ?Closure $rulesCallback;
};
