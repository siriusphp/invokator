<?php

declare(strict_types=1);

namespace Sirius\Invokator\Tests\Laravel\Fixtures;

class Dependency
{
    public function prefix(): string
    {
        return 'Hello, ';
    }
}
