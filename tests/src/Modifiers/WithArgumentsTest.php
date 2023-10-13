<?php

namespace Sirius\Invokator\Modifiers;

use Sirius\Invokator\Processors\SimpleCallablesProcessor;
use Sirius\Invokator\TestCase;
use function Sirius\Invokator\ref;
use function Sirius\Invokator\arg;
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

    public function test_modifier_with_refs()
    {
        $this->getContainer()->register('test_param', 'C');
        $processor = new SimpleCallablesProcessor($this->getInvoker());
        $processor->add('test', with_arguments(function ($param_1, $param_2, $param_3, $param_4) {
            static::$results[] = sprintf("anonymous function(%s, %s, %s, %s)", $param_1, $param_2, $param_3, $param_4);
        }, [arg(1), arg(0), ref('test_param'), 'D']));

        $processor->process('test', 'A', 'B');

        $this->assertSame([
            "anonymous function(B, A, C, D)",
        ], static::$results);
    }

    public function test_modifier_with_invoker_result()
    {
        $this->getContainer()->register('test_param', 'C');
        $processor = new SimpleCallablesProcessor($this->getInvoker());
        $processor->add('test', with_arguments(function ($param_1, $param_2, $param_3) {
            static::$results[] = sprintf("anonymous function(%s, %s, %s)", $param_1, $param_2, $param_3);
        }, [result_of('trim', ['   C   ']), arg(1), arg(0)]));

        $processor->process('test', 'A', 'B');

        $this->assertSame([
            "anonymous function(C, B, A)",
        ], static::$results);
    }
}
