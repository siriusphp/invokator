<?php

namespace Sirius\Invokator\Modifiers;

use Sirius\Invokator\Callables\CallableAction;
use Sirius\Invokator\TestCase;
use function Sirius\Invokator\arg;
use function Sirius\Invokator\ref;
use function Sirius\Invokator\result_of;
use function Sirius\Invokator\with_arguments;

class WithArgumentsTest extends TestCase
{
    static public $results = [];

    protected function setUp(): void
    {
        parent::setUp();
        static::$results = [];
    }

    public function test_modifier_with_refs(): void
    {
        $this->getContainer()->register('test_param', 'C');
        $action = new CallableAction($this->getInvoker());
        $action->add(with_arguments(function (string $param_1, $param_2, $param_3, $param_4): void {
            static::$results[] = sprintf("anonymous function(%s, %s, %s, %s)", $param_1, $param_2, $param_3, $param_4);
        }, [arg(1), arg(0), ref('test_param'), 'D']), 0, null);

        $action->run('A', 'B');

        $this->assertSame([
            "anonymous function(B, A, C, D)",
        ], static::$results);
    }

    public function test_modifier_with_invoker_result(): void
    {
        $this->getContainer()->register('test_param', 'C');
        $action = new CallableAction($this->getInvoker());
        $action->add(with_arguments(function (string $param_1, $param_2, $param_3): void {
            static::$results[] = sprintf("anonymous function(%s, %s, %s)", $param_1, $param_2, $param_3);
        }, [result_of('trim', ['   C   ']), arg(1), arg(0)]), 0, null);

        $action->run('A', 'B');

        $this->assertSame([
            "anonymous function(C, B, A)",
        ], static::$results);
    }
}
