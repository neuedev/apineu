<?php

namespace Neuedev\Apineu\Validator\Validators;

use DateTime;
use Exception;
use Neuedev\Apineu\Validator\Rule\RuleBag;
use Neuedev\Apineu\Validator\Validator;

class DateValidator extends Validator
{
    public static string $type = 'Afeefa.DateValidator';

    protected function rules(RuleBag $rules): void
    {
        $rules->add('date')
            ->default(true)
            ->message('{{ fieldLabel }} sollte ein Datum sein.')
            ->validate(function ($value) {
                // null may be okay, validate null in filled-rule
                if (is_null($value)) {
                    return true;
                }

                // value is date
                if ($value instanceof DateTime) {
                    return true;
                }

                // value is iso date string
                if (is_string($value)) {
                    // unsupported value
                    try {
                        new DateTime($value);
                    } catch (Exception $e) {
                        return false;
                    }

                    // value is an iso date
                    // YYYY-MM-DDTHH:mm:ss.sssZ
                    // https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Date/toISOString
                    // https://www.php.net/manual/de/class.datetimeinterface.php#datetimeinterface.constants.rfc3339-extended
                    $dateTime = DateTime::createFromFormat(DateTime::RFC3339_EXTENDED, $value);
                    if ($dateTime) {
                        return true;
                    }
                }

                return false;
            });
    }
}
