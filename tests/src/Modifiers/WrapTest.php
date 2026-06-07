<?php

namespace Sirius\Invokator;

use Sirius\Invokator\Callables\CallableAction;
use Sirius\Invokator\Utilities\SimpleCallables;

class WrapTest extends TestCase
{
    static public $results = [];

    protected function setUp(): void
    {
        parent::setUp();
        static::$results = [];
    }

    public function test_modifier(): void
    {
        $this->getContainer()->register(SimpleCallables::class, new SimpleCallables);
        $action = new CallableAction($this->getInvoker());
        $action->add(wrap(function (string $param_1, $param_2): void {
            static::$results[] = sprintf("anonymous function(%s, %s)", $param_1, $param_2);
        }, function ($next) {
            static::$results[] = 'From wrapper function';

            return $next();
        }), 0, null);

        $action->run('A', 'B');

        $this->assertSame([
            'From wrapper function',
            "anonymous function(A, B)",
        ], static::$results);
    }
}
