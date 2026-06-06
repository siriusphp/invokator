<?php

declare(strict_types=1);

namespace Sirius\Invokator\Tests\Laravel\Fixtures;

class SampleEvent
{
    public function __construct(public readonly string $payload)
    {
    }
}
