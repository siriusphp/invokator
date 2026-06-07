<?php

namespace Sirius\Invokator\Callables;

use Sirius\Invokator\TestCase;

class CallableFilterTest extends TestCase
{
    public function test_filter(): void
    {
        $filter = new CallableFilter($this->getInvoker());
        $filter->add(fn ($name): string => '   hello ' . $name, 0, 1);
        $filter->add('trim', 0, 1);
        $filter->add('ucwords', 0, 2);

        $result = $filter->run('world');

        $this->assertEquals('Hello World', $result);
    }
}
