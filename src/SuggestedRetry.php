<?php

declare(strict_types=1);

namespace Sirius\Invokator;

class SuggestedRetry
{
    public function __construct(public readonly int $retryAfter = 0)
    {
    }
}
