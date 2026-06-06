<?php

declare(strict_types=1);

namespace Sirius\Invokator\Utilities;

use Sirius\Invokator\SimpleStackProcessorTest;
use Sirius\Invokator\TestCase;

class DependencyClass
{
    static function add5($number): int|float
    {
        return $number + 5;
    }
}
