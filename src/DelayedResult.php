<?php

declare(strict_types=1);

namespace Sirius\StackRunner;

class DelayedResult
{
    public function __construct(public $value, public $retryAfter = 0)
    {
    }
}
