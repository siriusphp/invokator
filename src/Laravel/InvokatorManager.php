<?php

declare(strict_types=1);

namespace Sirius\Invokator\Laravel;

use Sirius\Invokator\CallableCollection;
use Sirius\Invokator\Event\Dispatcher;
use Sirius\Invokator\Processors\ActionsProcessor;
use Sirius\Invokator\Processors\FiltersProcessor;
use Sirius\Invokator\Processors\MiddlewareProcessor;
use Sirius\Invokator\Processors\PipelineProcessor;

/**
 * The object the `Invokator` facade and the `do_*` helpers proxy to.
 *
 * For pipeline/action/filter/middleware the call is overloaded on its arguments:
 * referencing a pattern by identifier only returns a {@see Registrar} for defining
 * callables, while passing extra arguments runs the pattern and returns its result.
 */
class InvokatorManager
{
    public function __construct(
        protected PipelineProcessor $pipelines,
        protected ActionsProcessor $actions,
        protected FiltersProcessor $filters,
        protected MiddlewareProcessor $middlewares,
        protected Dispatcher $dispatcher,
    ) {
    }

    public function pipeline(string $id, mixed ...$args): mixed
    {
        if ($args === []) {
            return new Registrar(fn (mixed $cb, int $priority, int $limit): CallableCollection => $this->pipelines->add($id, $cb, $priority));
        }

        return $this->pipelines->process($id, ...$args);
    }

    public function action(string $id, mixed ...$args): mixed
    {
        if ($args === []) {
            return new Registrar(fn (mixed $cb, int $priority, int $limit): CallableCollection => $this->actions->add($id, $cb, $priority, $limit));
        }

        return $this->actions->process($id, ...$args);
    }

    public function filter(string $id, mixed ...$args): mixed
    {
        if ($args === []) {
            return new Registrar(fn (mixed $cb, int $priority, int $limit): CallableCollection => $this->filters->add($id, $cb, $priority, $limit));
        }

        return $this->filters->process($id, ...$args);
    }

    public function middleware(string $id, mixed ...$args): mixed
    {
        if ($args === []) {
            return new Registrar(fn (mixed $cb, int $priority, int $limit): CallableCollection => $this->middlewares->add($id, $cb, $priority));
        }

        return $this->middlewares->process($id, ...$args);
    }

    /**
     * Subscribe listeners to an event. The event name is the event's class name (or the
     * value returned by HasEventName::getEventName()), so subscribe to `Event::class`.
     */
    public function event(string $eventName): Registrar
    {
        return new Registrar(fn (mixed $cb, int $priority, int $limit) => $this->dispatcher->subscribeTo($eventName, $cb, $priority));
    }

    /**
     * Dispatch a PSR-14 event object to its subscribed listeners.
     */
    public function dispatch(object $event): object
    {
        return $this->dispatcher->dispatch($event);
    }
}
