<?php

declare(strict_types=1);

namespace Sirius\StackRunner;

class DelayedResult
{
    public function __construct(public mixed $value, public int $retryAfter = 0)
    {
    }
}
