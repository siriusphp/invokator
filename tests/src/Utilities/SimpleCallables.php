<?php

namespace Sirius\Invokator\Utilities;

use Sirius\Invokator\SimpleStackProcessorTest;
use Sirius\Invokator\TestCase;

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
