<?php

namespace Sirius\StackRunner;

use Sirius\StackRunner\Processors\SimpleStackProcessor;
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
        $processor = new SimpleStackProcessor($this->getInvoker());
        $processor->add('test', wrap(function ($param_1, $param_2) {
            static::$results[] = sprintf("anonymous function(%s, %s)", $param_1, $param_2);
        }, function ($next) {
            static::$results[] = 'From wrapper function';

            return $next();
        }));

        $processor->process('test', 'A', 'B');

        $this->assertSame([
            'From wrapper function',
            "anonymous function(A, B)",
        ], static::$results);
    }
}
