<?php

namespace Sirius\StackRunner\Modifiers;

use Sirius\StackRunner\Locators\SimpleStackLocator;
use Sirius\StackRunner\TestCase;
use function Sirius\StackRunner\ref;
use function Sirius\StackRunner\arg;
use function Sirius\StackRunner\result_of;
use function Sirius\StackRunner\with_arguments;

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
        $locator = new SimpleStackLocator($this->getInvoker());
        $locator->add('test', with_arguments(function ($param_1, $param_2, $param_3, $param_4) {
            static::$results[] = sprintf("anonymous function(%s, %s, %s, %s)", $param_1, $param_2, $param_3, $param_4);
        }, [arg(1), arg(0), ref('test_param'), 'D']));

        $locator->process('test', 'A', 'B');

        $this->assertSame([
            "anonymous function(B, A, C, D)",
        ], static::$results);
    }

    public function test_modifier_with_invoker_result()
    {
        $this->getContainer()->register('test_param', 'C');
        $locator = new SimpleStackLocator($this->getInvoker());
        $locator->add('test', with_arguments(function ($param_1, $param_2, $param_3) {
            static::$results[] = sprintf("anonymous function(%s, %s, %s)", $param_1, $param_2, $param_3);
        }, [result_of('trim', ['   C   ']), arg(1), arg(0)]));

        $locator->process('test', 'A', 'B');

        $this->assertSame([
            "anonymous function(C, B, A)",
        ], static::$results);
    }
}
