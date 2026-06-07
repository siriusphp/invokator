<?php

namespace Sirius\Invokator\Modifiers;

use Sirius\Invokator\Callables\CallableAction;
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

    public function test_modifier(): void
    {
        $action = new CallableAction($this->getInvoker());
        $action->add(once(function (string $param_1, $param_2): void {
            static::$results[] = sprintf("anonymous function(%s, %s)", $param_1, $param_2);
        }), 0, null);

        $action->run('A', 'B');
        $action->run('A', 'B');
        $action->run('A', 'B');
        $action->run('A', 'B');

        $this->assertSame([
            "anonymous function(A, B)",
        ], static::$results);
    }
}
