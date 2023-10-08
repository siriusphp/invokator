<?php

namespace Sirius\StackRunner\Modifiers;

use Sirius\StackRunner\Locators\SimpleStackLocator;
use Sirius\StackRunner\TestCase;
use function Sirius\StackRunner\once;

class OnceTest extends TestCase
{
    static public $results = [];

    protected function setUp(): void
    {
        parent::setUp();
        static::$results = [];
    }

    public function test_modifier()
    {
        $locator = new SimpleStackLocator($this->getInvoker());
        $locator->add('test', once(function ($param_1, $param_2) {
            static::$results[] = sprintf("anonymous function(%s, %s)", $param_1, $param_2);
        }));

        $locator->process('test', 'A', 'B');
        $locator->process('test', 'A', 'B');
        $locator->process('test', 'A', 'B');
        $locator->process('test', 'A', 'B');

        return $this->assertSame([
            "anonymous function(A, B)",
        ], static::$results);
    }
}
