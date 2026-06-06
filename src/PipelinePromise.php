<?php

declare(strict_types=1);

namespace Sirius\Invokator;

class PipelinePromise
{
    /**
     * @param array<mixed> $params
     */
    public function __construct(public readonly mixed $value, public readonly CallableCollection $remainingCallables, public readonly array $params, public readonly int $retryAfter)
    {
    }
}
