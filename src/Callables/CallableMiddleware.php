<?php

declare(strict_types=1);

namespace Sirius\Invokator\Callables;

use Sirius\Invokator\CallableCollection;

/**
 * Runs callables as a middleware stack: every callable (except the innermost) receives the
 * run arguments plus a `$next` callback that delegates to the rest of the stack. A callable
 * decides whether to call `$next(...)` and what to do with its result.
 */
class CallableMiddleware extends AbstractCallableStack
{
    public function run(mixed ...$params): mixed
    {
        return $this->runStack($this->freshStack(), ...$params);
    }

    private function runStack(CallableCollection $stack, mixed ...$params): mixed
    {
        $result       = null;
        $nextCallable = $stack->isEmpty() ? null : $stack->extract();

        while ($nextCallable !== null) {
            if ($stack->isEmpty()) {
                $response = $this->invoker->invoke($nextCallable, ...$params);
            } else {
                $next          = fn ($result = null): mixed => $this->runStack($stack, ...$params);
                $paramsForNext = [...$params, $next];
                $response      = $this->invoker->invoke($nextCallable, ...$paramsForNext);
            }

            $result       = $response;
            $nextCallable = $stack->isEmpty() ? null : $stack->extract();
        }

        return $result;
    }
}
