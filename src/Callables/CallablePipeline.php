<?php

declare(strict_types=1);

namespace Sirius\Invokator\Callables;

use Sirius\Invokator\PipelinePromise;
use Sirius\Invokator\SuggestedResume;
use Sirius\Invokator\SuggestedRetry;

/**
 * Runs callables in sequence, passing the result of one as the (single) argument of the next.
 * The value returned by the last callable is the result of the pipeline.
 *
 * A callable may return a {@see SuggestedRetry} (retry itself later) or a {@see SuggestedResume}
 * (continue the remaining callables later), in which case run() returns a {@see PipelinePromise}
 * describing the deferred continuation.
 */
class CallablePipeline extends AbstractCallableStack
{
    public function run(mixed ...$params): mixed
    {
        $stack        = $this->freshStack();
        $result       = null;
        $nextCallable = $stack->isEmpty() ? null : $stack->extract();

        while ($nextCallable !== null) {
            $result = $this->invoker->invoke($nextCallable, ...$params);

            // SuggestedRetry is returned when $nextCallable fails during processing
            // but it knows that it might work in the future.
            if ($result instanceof SuggestedRetry) {
                $stack->add($nextCallable, PHP_INT_MIN);
                return new PipelinePromise($params[0], $stack, $params, $result->retryAfter);
            }
            // SuggestedResume is returned when $nextCallable was successful but
            // knows the continuation of the pipeline should happen with a delay.
            if ($result instanceof SuggestedResume) {
                return new PipelinePromise($result, $stack, $params, $result->delay);
            }

            $params       = [$result];
            $nextCallable = $stack->isEmpty() ? null : $stack->extract();
        }

        return $result;
    }
}
