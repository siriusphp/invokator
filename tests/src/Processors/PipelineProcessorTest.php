<?php

namespace Sirius\Invokator\Processors;

use Sirius\Invokator\TestCase;
use Sirius\Invokator\Utilities\SimpleCallables;

class PipelineProcessorTest extends TestCase
{
    public function test_pipeline_processor()
    {
        $this->getContainer()->register(SimpleCallables::class, new SimpleCallables);
        $processor = new PipelineProcessor($this->getInvoker());
        $processor->add('test', function ($name) {
            return '   hello ' . $name;
        }, 1, 1);
        $processor->add('test', 'trim', 1);
        $processor->add('test', 'ucwords', 1);

        $result = $processor->process('test', 'world');

        $this->assertEquals('Hello World', $result);
    }
}
