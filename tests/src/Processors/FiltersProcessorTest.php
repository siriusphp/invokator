<?php

namespace Sirius\StackRunner\Processors;

use Sirius\StackRunner\TestCase;
use Sirius\StackRunner\Utilities\SimpleCallables;

class FiltersProcessorTest extends TestCase
{
    public function test_filters_processor()
    {
        $this->getContainer()->register(SimpleCallables::class, new SimpleCallables);
        $processor = new FiltersProcessor($this->getInvoker());
        $processor->add('test', function ($name) {
            return '   hello ' . $name;
        }, 0, 1);
        $processor->add('test', 'trim', 1, 1);
        $processor->add('test', 'ucwords', 1, 2);

        $result = $processor->process('test', 'world');

        $this->assertEquals('Hello World', $result);
    }
}
