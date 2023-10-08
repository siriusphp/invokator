<?php

namespace Sirius\StackRunner\Locators;

use Sirius\StackRunner\TestCase;
use Sirius\StackRunner\Utilities\SimpleCallables;

class PipelineLocatorTest extends TestCase
{
    public function test_pipeline_locator()
    {
        $this->getContainer()->register(SimpleCallables::class, new SimpleCallables);
        $locator = new PipelineLocator($this->getInvoker());
        $locator->add('test', function ($name) {
            return '   hello ' . $name;
        }, 0, 1);
        $locator->add('test', 'trim', 1);
        $locator->add('test', 'ucwords', 1);

        $result = $locator->process('test', 'world');

        return $this->assertEquals('Hello World', $result);
    }
}
