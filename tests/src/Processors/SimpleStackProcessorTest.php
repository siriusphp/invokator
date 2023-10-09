<?php

namespace Sirius\StackRunner\Processors;

use Sirius\StackRunner\TestCase;
use Sirius\StackRunner\Utilities\SimpleCallables;

class SimpleStackProcessorTest extends TestCase
{
    public function test_simple_stack_processor()
    {
        $this->getContainer()->register(SimpleCallables::class, new SimpleCallables);
        $processor = new SimpleStackProcessor($this->getInvoker());
        $processor->add('test', function ($param_1, $param_2) {
            static::$results[] = sprintf("anonymous function(%s, %s)", $param_1, $param_2);
        });
        $processor->add('test', SimpleCallables::class . '::staticMethod');
        $processor->add('test', SimpleCallables::class . '@method');

        $processor->process('test', 'A', 'B');

        $this->assertSame([
            "anonymous function(A, B)",
            SimpleCallables::class . "::staticMethod(A, B)",
            SimpleCallables::class . "@method(A, B)",
        ], static::$results);
    }
}
