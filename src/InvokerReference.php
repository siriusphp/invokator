<?php

declare(strict_types=1);

namespace Sirius\StackRunner;

class InvokerReference
{
    public function __construct(public string|int $reference)
    {
    }
}
