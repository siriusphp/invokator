<?php

namespace Sirius\StackRunner\Utilities;

use Sirius\StackRunner\SimpleStackProcessorTest;
use Sirius\StackRunner\TestCase;

class SimpleCallables
{

    static function staticMethod(...$params)
    {
        TestCase::$results[] = sprintf('%s::%s(%s)', __CLASS__, __FUNCTION__, implode(', ', $params));
    }

    static function method(...$params)
    {
        TestCase::$results[] = sprintf('%s@%s(%s)', __CLASS__, __FUNCTION__, implode(', ', $params));
    }
}
