<?php

namespace Sirius\Invokator\CommandBus;

use Sirius\Invokator\SimpleStackProcessorTest;
use Sirius\Invokator\TestCase;

class SampleCommand
{
    public function __construct(public int $first, public int $second)
    {
    }
}
