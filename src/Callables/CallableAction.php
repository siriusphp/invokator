<?php

declare(strict_types=1);

namespace Sirius\Invokator\Callables;

use function Sirius\Invokator\limit_arguments;

/**
 * WordPress-style action: runs every callable for its side effects, passing the run arguments
 * to each one. Return values are ignored and run() always returns null.
 *
 * By default each callable receives a single argument (the WordPress default); pass an
 * `$argumentsLimit` to widen this, or `null` to pass every argument unchanged — the latter
 * gives the "simple collection" behaviour (same signature for every callable, returns ignored).
 */
class CallableAction extends AbstractCallableStack
{
    #[\Override]
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
        $nextCallable = $stack->isEmpty() ? null : $stack->extract();

        while ($nextCallable !== null) {
            $this->invoker->invoke($nextCallable, ...$params);
            $nextCallable = $stack->isEmpty() ? null : $stack->extract();
        }

        return null;
    }
}
