<?php

namespace Sirius\Invokator\Callables;

use Sirius\Invokator\TestCase;

class CallableMiddlewareTest extends TestCase
{
    public function test_middleware(): void
    {
        $middleware = new CallableMiddleware($this->getInvoker());
        $middleware->add(fn ($name, $next = null): string => ucwords((string) $next($name)));
        $middleware->add(fn ($name, $next = null): string => 'Hello ' . $next($name));
        $middleware->add(fn ($name, $next = null) => $name);

        $result = $middleware->run('world');

        $this->assertEquals('Hello World', $result);
    }
}
