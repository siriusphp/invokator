<?php

declare(strict_types=1);

namespace Sirius\Invokator;

class SuggestedRetry
{
    public function __construct(public int $retryAfter = 0)
    {
    }
}
