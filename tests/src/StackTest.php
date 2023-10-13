<?php

namespace Sirius\Invokator;

class StackTest extends TestCase
{
    function test_priorities_are_respected()
    {
        $stack = new CallableCollection();
        $stack->add('callable_1', 10);
        $stack->add('callable_2', 10);
        $stack->add('callable_3', 100);
        $stack->add('callable_4', 100);
        $stack->add('callable_5');
        $stack->add('callable_6');

        $this->assertSame([
            'callable_3',
            'callable_4',
            'callable_1',
            'callable_2',
            'callable_5',
            'callable_6',
        ], $this->getCallablesFromStack($stack));
    }

    public function test_serialization_of_simple_stack()
    {
        $stack = new CallableCollection();
        $stack->add('callable_1', 10);
        $stack->add('callable_2', 10);
        $stack->add('callable_3', 100);
        $stack->add('callable_4', 100);
        $stack->add('callable_5');
        $stack->add('callable_6');

        $this->assertSame([
            'callable_3',
            'callable_4',
            'callable_1',
            'callable_2',
            'callable_5',
            'callable_6',
        ], $this->getCallablesFromStack(unserialize(serialize($stack))));
    }
}
