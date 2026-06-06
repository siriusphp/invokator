<?php

declare(strict_types=1);

namespace Sirius\Invokator;

class InvokerResult
{
    /**
     * @param array<mixed> $params
     */
    public function __construct(public readonly mixed $callable, public readonly array $params = [])
    {
    }
}
