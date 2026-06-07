<?php

declare(strict_types=1);

namespace Sirius\Invokator\Callables;

use Sirius\Invokator\CallableCollection;
use Sirius\Invokator\Invoker;
use Sirius\Invokator\RunnableCallables;

/**
 * Base for the self-contained callable stacks (pipeline, middleware, filter, action).
 *
 * It owns a single priority-ordered {@see CallableCollection} and the {@see Invoker} used to
 * execute its callables. Subclasses only implement {@see run()} with their own strategy; they
 * run against a fresh copy of the collection (see {@see freshStack()}) so a stack can be run
 * repeatedly without losing its registrations.
 */
abstract class AbstractCallableStack implements RunnableCallables
{
    protected CallableCollection $callables;

    public function __construct(public Invoker $invoker)
    {
        $this->callables = new CallableCollection();
    }

    public function add(mixed $callable, int $priority = 0): static
    {
        $this->callables->add($callable, $priority);

        return $this;
    }

    /**
     * Cloning a stack must not share its underlying queue with the original
     * (the {@see CommandBus} relies on this).
     */
    public function __clone(): void
    {
        $this->callables = clone $this->callables;
    }

    /**
     * A disposable copy of the registered callables, safe to consume via extract().
     */
    protected function freshStack(): CallableCollection
    {
        return clone $this->callables;
    }
}
