<?php

declare(strict_types=1);

namespace Sirius\Invokator\Callables;

use function Sirius\Invokator\limit_arguments;

/**
 * WordPress-style filter: runs callables in sequence, threading the first argument (the value
 * being filtered) through each one while keeping any extra context arguments untouched. The
 * value returned by the last callable is the filtered result.
 *
 * By default each callable receives a single argument; pass an `$argumentsLimit` to widen this
 * (the value plus N-1 context arguments), or `null` to pass every argument unchanged.
 */
class CallableFilter extends AbstractCallableStack
{
    public function add(mixed $callable, int $priority = 0, ?int $argumentsLimit = 1): static
    {
        if ($argumentsLimit !== null) {
            $callable = limit_arguments($callable, $argumentsLimit);
        }

        return parent::add($callable, $priority);
    }

    public function run(mixed ...$params): mixed
    {
        $stack        = $this->freshStack();
        $result       = null;
        $nextCallable = $stack->isEmpty() ? null : $stack->extract();

        while ($nextCallable !== null) {
            $result    = $this->invoker->invoke($nextCallable, ...$params);
            $params[0] = $result;

            $nextCallable = $stack->isEmpty() ? null : $stack->extract();
        }

        return $result;
    }
}
