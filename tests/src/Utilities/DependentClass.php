<?php

declare(strict_types=1);

namespace Sirius\Invokator\Utilities;

use Sirius\Invokator\SimpleStackProcessorTest;
use Sirius\Invokator\TestCase;

class DependentClass
{
    static function multiply($firstNumber, DependencyClass $dep, $secondNumber): int|float
    {
        return $firstNumber * $dep->add5($secondNumber);
    }
}
