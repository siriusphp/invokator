<?php

declare(strict_types=1);

namespace Sirius\Invokator\Utilities;

use Sirius\Invokator\SimpleStackProcessorTest;
use Sirius\Invokator\TestCase;

class SimpleCallables
{

    static function staticMethod(...$params): void
    {
        TestCase::$results[] = sprintf('%s::%s(%s)', self::class, __FUNCTION__, implode(', ', $params));
    }

    static function method(...$params): void
    {
        TestCase::$results[] = sprintf('%s@%s(%s)', self::class, __FUNCTION__, implode(', ', $params));
    }
}
