<?php

namespace Sirius\Invokator\Modifiers;

use Sirius\Invokator\Processors\SimpleCallablesProcessor;
use Sirius\Invokator\TestCase;
use function Sirius\Invokator\once;

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
        $processor = new SimpleCallablesProcessor($this->getInvoker());
        $processor->add('test', once(function ($param_1, $param_2) {
            static::$results[] = sprintf("anonymous function(%s, %s)", $param_1, $param_2);
        }));

        $processor->process('test', 'A', 'B');
        $processor->process('test', 'A', 'B');
        $processor->process('test', 'A', 'B');
        $processor->process('test', 'A', 'B');

        $this->assertSame([
            "anonymous function(A, B)",
        ], static::$results);
    }
}
