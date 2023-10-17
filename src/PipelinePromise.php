<?php

declare(strict_types=1);

namespace Sirius\Invokator;

class PipelinePromise
{
    /**
     * @param array<mixed> $params
     */
    public function __construct(public mixed $value, public CallableCollection $remainingCallables, public array $params, public int $retryAfter)
    {
    }
}
