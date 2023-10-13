<?php

namespace Sirius\Invokator\Utilities;

use Sirius\Invokator\SimpleStackProcessorTest;
use Sirius\Invokator\TestCase;

class DependentClass
{
    static function multiply($firstNumber, DependencyClass $dep, $secondNumber)
    {
        return $firstNumber * $dep->add5($secondNumber);
    }
}
