<?php

declare(strict_types=1);

namespace Sirius\Invokator;

class SuggestedResume
{
    public function __construct(public readonly mixed $value, public readonly int $delay = 0)
    {
    }
}
