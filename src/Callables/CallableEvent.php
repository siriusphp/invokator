<?php

declare(strict_types=1);

namespace Sirius\Invokator\Callables;

use Sirius\Invokator\Event\Dispatcher;

/**
 * Uniform `add()`/`run()` handle over the PSR-14 {@see Dispatcher} for a single event name.
 *
 * `add()` subscribes a listener, `once()` subscribes a one-shot listener, and `run()` dispatches
 * an event object (routed by the object's own class, like any PSR-14 dispatch).
 */
final class CallableEvent
{
    public function __construct(private readonly Dispatcher $dispatcher, private readonly string $eventName)
    {
    }

    public function add(mixed $listener, int $priority = 0): static
    {
        $this->dispatcher->subscribeTo($this->eventName, $listener, $priority);

        return $this;
    }

    public function once(mixed $listener, int $priority = 0): static
    {
        $this->dispatcher->subscribeOnceTo($this->eventName, $listener, $priority);

        return $this;
    }

    public function run(object $event): object
    {
        return $this->dispatcher->dispatch($event);
    }
}
