<?php

namespace Sirius\StackRunner\Locators;

use Sirius\StackRunner\TestCase;
use Sirius\StackRunner\Utilities\SimpleCallables;

class FiltersLocatorTest extends TestCase
{
    public function test_filters_locator()
    {
        $this->getContainer()->register(SimpleCallables::class, new SimpleCallables);
        $locator = new FiltersLocator($this->getInvoker());
        $locator->add('test', function ($name) {
            return '   hello ' . $name;
        }, 0, 1);
        $locator->add('test', 'trim', 1, 1);
        $locator->add('test', 'ucwords', 1, 2);

        $result = $locator->process('test', 'world');

        $this->assertEquals('Hello World', $result);
    }
}
