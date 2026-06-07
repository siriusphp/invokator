<?php

declare(strict_types=1);

namespace Sirius\Invokator\CommandBus;

class SampleHandler
{
    public function handle(SampleCommand $command): int
    {
        return $command->first + $command->second;
    }
}
