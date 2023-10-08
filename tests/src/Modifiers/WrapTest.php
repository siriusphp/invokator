<?php

namespace Sirius\StackRunner;

use Sirius\StackRunner\Locators\SimpleStackLocator;
use Sirius\StackRunner\Utilities\SimpleCallables;

class WrapTest extends TestCase
{
    static public $results = [];

    protected function setUp(): void
    {
        parent::setUp();
        static::$results = [];
    }

    public function test_modifier()
    {
        $this->getContainer()->register(SimpleCallables::class, new SimpleCallables);
        $locator = new SimpleStackLocator($this->getInvoker());
        $locator->add('test', wrap(function ($param_1, $param_2) {
            static::$results[] = sprintf("anonymous function(%s, %s)", $param_1, $param_2);
        }, function ($next) {
            static::$results[] = 'From wrapper function';

            return $next();
        }));

        $locator->process('test', 'A', 'B');

        return $this->assertSame([
            'From wrapper function',
            "anonymous function(A, B)",
        ], static::$results);
    }
}
