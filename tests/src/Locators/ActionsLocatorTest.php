<?php

namespace Sirius\StackRunner\Locators;

use Sirius\StackRunner\TestCase;
use Sirius\StackRunner\Utilities\SimpleCallables;

class ActionsLocatorTest extends TestCase
{
    public function test_actions_locator()
    {
        $this->getContainer()->register(SimpleCallables::class, new SimpleCallables);
        $locator = new ActionsLocator($this->getInvoker());
        $locator->add('test', function ($param_1) {
            static::$results[] = sprintf("anonymous function(%s)", $param_1);
        }, 0, 1);
        $locator->add('test', SimpleCallables::class . '::staticMethod', 1, 1);
        $locator->add('test', SimpleCallables::class . '@method', 1, 2);

        $locator->process('test', 'A', 'B');

        $this->assertSame([
            "anonymous function(A)",
            SimpleCallables::class . "::staticMethod(A)",
            SimpleCallables::class . "@method(A, B)",
        ], static::$results);
    }
}
