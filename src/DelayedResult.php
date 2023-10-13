<?php

declare(strict_types=1);

namespace Sirius\Invokator;

class DelayedResult
{
    public function __construct(public mixed $value, public int $retryAfter = 0)
    {
    }
}
