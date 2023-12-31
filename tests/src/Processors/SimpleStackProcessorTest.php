<?php

namespace Sirius\Invokator\Processors;

use Sirius\Invokator\TestCase;
use Sirius\Invokator\Utilities\SimpleCallables;

class SimpleStackProcessorTest extends TestCase
{
    public function test_simple_stack_processor()
    {
        $this->getContainer()->register(SimpleCallables::class, new SimpleCallables);
        $processor = new SimpleCallablesProcessor($this->getInvoker());
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

    public function test_execution_priority()
    {
        $this->getContainer()->register(SimpleCallables::class, new SimpleCallables);
        $processor = new SimpleCallablesProcessor($this->getInvoker());
        $processor->add('test', function ($param_1, $param_2) {
            static::$results[] = sprintf("anonymous function(%s, %s)", $param_1, $param_2);
        });
        $processor->add('test', SimpleCallables::class . '@method', 100);
        $processor->add('test', SimpleCallables::class . '::staticMethod');

        $processor->process('test', 'A', 'B');

        $this->assertSame([
            SimpleCallables::class . "@method(A, B)",
            "anonymous function(A, B)",
            SimpleCallables::class . "::staticMethod(A, B)",
        ], static::$results);
    }
}
