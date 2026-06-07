<?php

declare(strict_types=1);

namespace Sirius\Invokator;

use Sirius\Invokator\Callables\CallableAction;
use Sirius\Invokator\Callables\CallableCommand;
use Sirius\Invokator\Callables\CallableEvent;
use Sirius\Invokator\Callables\CallableFilter;
use Sirius\Invokator\Callables\CallableMiddleware;
use Sirius\Invokator\Callables\CallablePipeline;
use Sirius\Invokator\Callables\CommandBus;
use Sirius\Invokator\Event\Dispatcher;
use Sirius\Invokator\Event\ListenerProvider;

/**
 * A framework-agnostic registry/facade over every callable pattern.
 *
 * Each pattern method returns a fluent object you register callables on and run:
 *
 *     $invokator->pipeline('slug', 'trim', 'strtolower'); // bulk register
 *     $invokator->pipeline('slug')->add(fn ($t) => "$t!", 10); // per-callable priority
 *     $invokator->pipeline('slug')->run('Hello'); // run
 *
 * Stack patterns (pipeline/middleware/filter/action) are cached per identifier because their
 * state lives in the returned object. Event and command handles are thin wrappers over the
 * shared {@see Dispatcher} / {@see CommandBus}, so a fresh one is handed out on each call.
 */
final class Invokator
{
    /**
     * @var array<string, CallablePipeline>
     */
    private array $pipelines = [];

    /**
     * @var array<string, CallableMiddleware>
     */
    private array $middlewares = [];

    /**
     * @var array<string, CallableFilter>
     */
    private array $filters = [];

    /**
     * @var array<string, CallableAction>
     */
    private array $actions = [];

    private readonly Dispatcher $dispatcher;

    private readonly CommandBus $commandBus;

    public function __construct(
        public readonly Invoker $invoker,
        ?Dispatcher $dispatcher = null,
        ?CommandBus $commandBus = null,
    ) {
        $this->dispatcher = $dispatcher ?? new Dispatcher(new ListenerProvider(), $invoker);
        $this->commandBus = $commandBus ?? new CommandBus($invoker);
    }

    public function pipeline(string $id, mixed ...$callables): CallablePipeline
    {
        $pipeline = $this->pipelines[$id] ??= new CallablePipeline($this->invoker);
        foreach ($callables as $callable) {
            $pipeline->add($callable);
        }

        return $pipeline;
    }

    public function middleware(string $id, mixed ...$callables): CallableMiddleware
    {
        $middleware = $this->middlewares[$id] ??= new CallableMiddleware($this->invoker);
        foreach ($callables as $callable) {
            $middleware->add($callable);
        }

        return $middleware;
    }

    public function filter(string $id, mixed ...$callables): CallableFilter
    {
        $filter = $this->filters[$id] ??= new CallableFilter($this->invoker);
        foreach ($callables as $callable) {
            $filter->add($callable);
        }

        return $filter;
    }

    public function action(string $id, mixed ...$callables): CallableAction
    {
        $action = $this->actions[$id] ??= new CallableAction($this->invoker);
        foreach ($callables as $callable) {
            $action->add($callable);
        }

        return $action;
    }

    /**
     * Subscribe listeners to an event. The event name is the event's class name (or the value
     * returned by HasEventName::getEventName()), so reference it as `Event::class`.
     */
    public function event(string $eventName, mixed ...$listeners): CallableEvent
    {
        $event = new CallableEvent($this->dispatcher, $eventName);
        foreach ($listeners as $listener) {
            $event->add($listener);
        }

        return $event;
    }

    public function command(string $commandClass, mixed ...$middleware): CallableCommand
    {
        $command = new CallableCommand($this->commandBus, $commandClass);
        foreach ($middleware as $callable) {
            $command->add($callable);
        }

        return $command;
    }

    /**
     * Dispatch a PSR-14 event object to its subscribed listeners.
     */
    public function dispatch(object $event): object
    {
        return $this->dispatcher->dispatch($event);
    }

    /**
     * Send a command object through its middleware to its handler.
     */
    public function handle(object $command): mixed
    {
        return $this->commandBus->handle($command);
    }

    public function dispatcher(): Dispatcher
    {
        return $this->dispatcher;
    }

    public function commandBus(): CommandBus
    {
        return $this->commandBus;
    }
}
