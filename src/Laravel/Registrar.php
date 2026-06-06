<?php

declare(strict_types=1);

namespace Sirius\Invokator\Laravel;

/**
 * Chainable builder returned when a pattern is referenced by identifier only
 * (e.g. `Invokator::pipeline('id')->add(...)`).
 *
 * Every `add()` routes through the closure provided by the manager, which calls the
 * underlying processor's own `add()` method. This matters for actions and filters whose
 * `add()` wraps the callable with `limit_arguments(...)` — a behaviour that would be lost
 * if we returned and mutated a raw CallableCollection instead.
 */
final class Registrar
{
    /**
     * @param \Closure(mixed, int, int): mixed $adder
     */
    public function __construct(private \Closure $adder)
    {
    }

    public function add(mixed $callable, int $priority = 0, int $argumentsLimit = 1): self
    {
        ($this->adder)($callable, $priority, $argumentsLimit);

        return $this;
    }
}
