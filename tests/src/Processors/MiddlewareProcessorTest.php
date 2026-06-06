<?php

namespace Sirius\Invokator\Processors;

use Sirius\Invokator\TestCase;
use Sirius\Invokator\Utilities\SimpleCallables;

class MiddlewareProcessorTest extends TestCase
{
    public function test_middleware_processor(): void
    {
        $this->getContainer()->register(SimpleCallables::class, new SimpleCallables);
        $processor = new MiddlewareProcessor($this->getInvoker());
        $processor->add('test', fn($name, $next = null): string => ucwords((string) $next($name)));
        $processor->add('test', fn($name, $next = null): string => 'Hello ' . $next($name));
        $processor->add('test', fn($name, $next = null) => $name);

        $result = $processor->process('test', 'world');

        $this->assertEquals('Hello World', $result);
    }
}
