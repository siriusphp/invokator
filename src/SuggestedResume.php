<?php

declare(strict_types=1);

namespace Sirius\Invokator;

class SuggestedResume
{
    public function __construct(public mixed $value, public int $delay = 0)
    {
    }
}
