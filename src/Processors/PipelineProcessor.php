<?php

declare(strict_types=1);

namespace Sirius\StackRunner\Processors;

use Sirius\StackRunner\DelayedResult;
use Sirius\StackRunner\PipelinePromise;
use Sirius\StackRunner\Stack;

class PipelineProcessor extends SimpleStackProcessor
{
    /**
     * @param array<mixed> $params
     */

    public function processStack(Stack $stack, ...$params): mixed
    {
        $result       = null;
        $nextCallable = $stack->extract();

        while ($nextCallable !== null) {
            $result = $this->invoker->invoke($nextCallable, ...$params);

            if ($result instanceof DelayedResult) {
                return new PipelinePromise($result->value, $stack, $params, $result->retryAfter);
            }
            $params = [$result];

            $nextCallable = $stack->isEmpty() ? null : $stack->extract();
        }

        return $result;
    }

    /**
     * @param array<mixed> $params
     */
    public function resumeStack(Stack $remainingStack, mixed $previousValue, ...$params): mixed
    {
        return null;
    }

}
