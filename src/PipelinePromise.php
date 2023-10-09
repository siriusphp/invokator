<?php

declare(strict_types=1);

namespace Sirius\StackRunner;

class PipelinePromise
{
    /**
     * @param array<mixed> $params
     */
    public function __construct(public mixed $value, public Stack $remainingStack, public array $params, public int $retryAfter)
    {
    }
}
