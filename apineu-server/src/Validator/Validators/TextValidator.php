<?php

namespace Neuedev\Apineu\Validator\Validators;

use Neuedev\Apineu\Validator\Sanitizer\SanitizerBag;

class TextValidator extends StringValidator
{
    public static string $type = 'Afeefa.TextValidator';

    protected function sanitizers(SanitizerBag $sanitizers): void
    {
        parent::sanitizers($sanitizers);

        $sanitizers->get('collapseWhite')
            ->default(null);
    }
}
