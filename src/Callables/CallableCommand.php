<?php

declare(strict_types=1);

namespace Sirius\Invokator\Callables;

/**
 * Uniform `add()`/`run()` handle over the {@see CommandBus} for a single command class.
 *
 * `add()` registers middleware, `handledBy()` binds an explicit handler, and `run()` dispatches
 * a command object through the bus.
 */
final class CallableCommand
{
    public function __construct(private readonly CommandBus $bus, private readonly string $commandClass)
    {
    }

    public function add(mixed $middleware, int $priority = 0): static
    {
        $this->bus->addMiddleware($this->commandClass, $middleware, $priority);

        return $this;
    }

    public function handledBy(mixed $handler): static
    {
        $this->bus->register($this->commandClass, $handler);

        return $this;
    }

    public function run(object $command): mixed
    {
        return $this->bus->handle($command);
    }
}
