<?php declare(strict_types = 1);

namespace Spagi\EmailParser\helpers;

use Exception;

final class Strings
{

    public function __construct()
    {
        throw new Exception('Contructor for this class can not be allowed.');
    }

    public static function isEmpty(string $value): bool
    {
        return mb_strlen(trim($value)) === 0;
    }

}
