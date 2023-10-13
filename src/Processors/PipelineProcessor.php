<?php

declare(strict_types=1);

namespace Sirius\Invokator\Processors;

use Sirius\Invokator\DelayedResult;
use Sirius\Invokator\PipelinePromise;
use Sirius\Invokator\CallableCollection;

class PipelineProcessor extends SimpleCallablesProcessor
{
    /**
     * @param array<mixed> $params
     */

    public function processCollection(CallableCollection $stack, ...$params): mixed
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
    public function resumeStack(CallableCollection $remainingStack, mixed $previousValue, ...$params): mixed
    {
        return null;
    }

}
