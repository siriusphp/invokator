<?php

namespace Sirius\StackRunner\Locators;

use Sirius\StackRunner\TestCase;
use Sirius\StackRunner\Utilities\SimpleCallables;

class MiddlewareLocatorTest extends TestCase
{
    public function test_middleware_locator()
    {
        $this->getContainer()->register(SimpleCallables::class, new SimpleCallables);
        $locator = new MiddlewareLocator($this->getInvoker());
        $locator->add('test', function ($name, $next = null) {
            return ucwords($next($name));
        });
        $locator->add('test', function ($name, $next = null) {
            return 'Hello ' . $next($name);
        });
        $locator->add('test', function ($name, $next = null) {
            return $name;
        });

        $result = $locator->process('test', 'world');

        $this->assertEquals('Hello World', $result);
    }
}
