<?php

declare(strict_types=1);

namespace Sirius\StackRunner;

class InvokerResult
{
    /**
     * @param mixed $callable
     * @param array<mixed> $params
     */
    public function __construct(public mixed $callable, public array $params = [])
    {
    }
}
