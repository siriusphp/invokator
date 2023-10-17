<?php

declare(strict_types=1);

namespace Sirius\Invokator\Processors;

use Sirius\Invokator\SuggestedResume;
use Sirius\Invokator\SuggestedRetry;
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

            // SuggestedRetry is returned when $nextCallable fails during processing
            // but it knows how that it might work in the future
            if ($result instanceof SuggestedRetry) {
                $stack->add($nextCallable, PHP_INT_MIN);
                return new PipelinePromise($params[0], $stack, $params, $result->retryAfter);
            }
            // SuggestedResume is returned when $nextCallable was succesful but
            // knows the continuation of the pipeline should happen with a delay.
            if ($result instanceof SuggestedResume) {
                return new PipelinePromise($result, $stack, $params, $result->delay);
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
