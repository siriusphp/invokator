<?php

declare(strict_types=1);

namespace Sirius\Invokator\Utilities;


class DependentClass
{
    public static function multiply($firstNumber, DependencyClass $dep, $secondNumber): int|float
    {
        return $firstNumber * $dep->add5($secondNumber);
    }
}
