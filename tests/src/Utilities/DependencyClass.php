<?php

declare(strict_types=1);

namespace Sirius\Invokator\Utilities;


class DependencyClass
{
    public static function add5($number): int|float
    {
        return $number + 5;
    }
}
