<?php

namespace Sirius\StackRunner\Processors;

use Sirius\StackRunner\TestCase;
use Sirius\StackRunner\Utilities\SimpleCallables;

class ActionsProcessorTest extends TestCase
{
    public function test_actions_processor()
    {
        $this->getContainer()->register(SimpleCallables::class, new SimpleCallables);
        $processor = new ActionsProcessor($this->getInvoker());
        $processor->add('test', function ($param_1) {
            static::$results[] = sprintf("anonymous function(%s)", $param_1);
        }, 0, 1);
        $processor->add('test', SimpleCallables::class . '::staticMethod', 1, 1);
        $processor->add('test', SimpleCallables::class . '@method', 1, 2);

        $processor->process('test', 'A', 'B');

        $this->assertSame([
            "anonymous function(A)",
            SimpleCallables::class . "::staticMethod(A)",
            SimpleCallables::class . "@method(A, B)",
        ], static::$results);
    }
}
