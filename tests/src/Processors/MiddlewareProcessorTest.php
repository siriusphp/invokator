<?php

namespace Sirius\StackRunner\Processors;

use Sirius\StackRunner\TestCase;
use Sirius\StackRunner\Utilities\SimpleCallables;

class MiddlewareProcessorTest extends TestCase
{
    public function test_middleware_processor()
    {
        $this->getContainer()->register(SimpleCallables::class, new SimpleCallables);
        $processor = new MiddlewareProcessor($this->getInvoker());
        $processor->add('test', function ($name, $next = null) {
            return ucwords($next($name));
        });
        $processor->add('test', function ($name, $next = null) {
            return 'Hello ' . $next($name);
        });
        $processor->add('test', function ($name, $next = null) {
            return $name;
        });

        $result = $processor->process('test', 'world');

        $this->assertEquals('Hello World', $result);
    }
}
