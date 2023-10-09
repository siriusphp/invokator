<?php

namespace Sirius\StackRunner\Locators;

use Sirius\StackRunner\TestCase;
use Sirius\StackRunner\Utilities\SimpleCallables;

class SimpleStackLocatorTest extends TestCase
{
    public function test_simple_stack_locator()
    {
        $this->getContainer()->register(SimpleCallables::class, new SimpleCallables);
        $locator = new SimpleStackLocator($this->getInvoker());
        $locator->add('test', function ($param_1, $param_2) {
            static::$results[] = sprintf("anonymous function(%s, %s)", $param_1, $param_2);
        });
        $locator->add('test', SimpleCallables::class . '::staticMethod');
        $locator->add('test', SimpleCallables::class . '@method');

        $locator->process('test', 'A', 'B');

        $this->assertSame([
            "anonymous function(A, B)",
            SimpleCallables::class . "::staticMethod(A, B)",
            SimpleCallables::class . "@method(A, B)",
        ], static::$results);
    }
}
