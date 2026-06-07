<?php

namespace Sirius\Invokator\Callables;

use Sirius\Invokator\TestCase;
use Sirius\Invokator\Utilities\SimpleCallables;

class CallableActionTest extends TestCase
{
    public function test_action_limits_arguments_by_default(): void
    {
        $this->getContainer()->register(SimpleCallables::class, new SimpleCallables);
        $action = new CallableAction($this->getInvoker());
        $action->add(function (string $param_1): void {
            static::$results[] = sprintf("anonymous function(%s)", $param_1);
        }, 0, 1);
        $action->add(SimpleCallables::class . '::staticMethod', 0, 1);
        $action->add(SimpleCallables::class . '@method', 0, 2);

        $result = $action->run('A', 'B');

        $this->assertNull($result);
        $this->assertSame([
            "anonymous function(A)",
            SimpleCallables::class . "::staticMethod(A)",
            SimpleCallables::class . "@method(A, B)",
        ], static::$results);
    }

    public function test_a_null_arguments_limit_passes_every_argument(): void
    {
        $this->getContainer()->register(SimpleCallables::class, new SimpleCallables);
        $action = new CallableAction($this->getInvoker());
        $action->add(function (string $param_1, $param_2): void {
            static::$results[] = sprintf("anonymous function(%s, %s)", $param_1, $param_2);
        }, 0, null);
        $action->add(SimpleCallables::class . '::staticMethod', 0, null);
        $action->add(SimpleCallables::class . '@method', 0, null);

        $action->run('A', 'B');

        $this->assertSame([
            "anonymous function(A, B)",
            SimpleCallables::class . "::staticMethod(A, B)",
            SimpleCallables::class . "@method(A, B)",
        ], static::$results);
    }

    public function test_execution_priority(): void
    {
        $this->getContainer()->register(SimpleCallables::class, new SimpleCallables);
        $action = new CallableAction($this->getInvoker());
        $action->add(function (string $param_1, $param_2): void {
            static::$results[] = sprintf("anonymous function(%s, %s)", $param_1, $param_2);
        }, 0, null);
        $action->add(SimpleCallables::class . '@method', 100, null);
        $action->add(SimpleCallables::class . '::staticMethod', 0, null);

        $action->run('A', 'B');

        $this->assertSame([
            SimpleCallables::class . "@method(A, B)",
            "anonymous function(A, B)",
            SimpleCallables::class . "::staticMethod(A, B)",
        ], static::$results);
    }
}
