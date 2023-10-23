<?php

namespace Sirius\Invokator\CommandBus;

use Sirius\Invokator\SimpleStackProcessorTest;
use Sirius\Invokator\TestCase;

class SampleHandler
{
    public function handle(SampleCommand $command)
    {
        return $command->first + $command->second;
    }
}
