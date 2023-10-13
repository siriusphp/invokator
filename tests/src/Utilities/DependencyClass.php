<?php

namespace Sirius\Invokator\Utilities;

use Sirius\Invokator\SimpleStackProcessorTest;
use Sirius\Invokator\TestCase;

class DependencyClass
{
    static function add5($number)
    {
        return $number + 5;
    }
}
