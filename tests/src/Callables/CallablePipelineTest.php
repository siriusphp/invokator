<?php

namespace Sirius\Invokator\Callables;

use Sirius\Invokator\TestCase;

class CallablePipelineTest extends TestCase
{
    public function test_pipeline(): void
    {
        $pipeline = new CallablePipeline($this->getInvoker());
        $pipeline->add(fn ($name): string => '   hello ' . $name, 1);
        $pipeline->add('trim', 1);
        $pipeline->add('ucwords', 1);

        $result = $pipeline->run('world');

        $this->assertEquals('Hello World', $result);
    }

    public function test_it_can_be_run_more_than_once(): void
    {
        $pipeline = new CallablePipeline($this->getInvoker());
        $pipeline->add(fn ($name): string => 'hello ' . $name);

        $this->assertEquals('hello a', $pipeline->run('a'));
        $this->assertEquals('hello b', $pipeline->run('b'));
    }
}
