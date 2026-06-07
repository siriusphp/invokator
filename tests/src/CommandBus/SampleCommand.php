<?php

declare(strict_types=1);

namespace Sirius\Invokator\CommandBus;

class SampleCommand
{
    public function __construct(public int $first, public int $second)
    {
    }
}
