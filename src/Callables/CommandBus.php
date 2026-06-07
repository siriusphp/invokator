<?php

declare(strict_types=1);

namespace Sirius\Invokator\Callables;

use Sirius\Invokator\Invoker;

/**
 * Routes a command object through its (optional) middleware to a single handler.
 *
 * Middleware is registered per command class and backed by a {@see CallableMiddleware} stack;
 * the handler is appended as the innermost callable at {@see handle()} time. Handlers can be
 * registered explicitly via {@see register()} or auto-discovered (`FooCommand` -> `FooHandler`,
 * using its `handle`/`__invoke` method).
 */
class CommandBus
{
    /**
     * @var array<string, CallableMiddleware>
     */
    protected array $middlewares = [];

    /**
     * Handlers are stored separately so they don't have to be defined before the middleware;
     * the relevant one is appended to the stack right before the command is handled.
     *
     * @var array<string, mixed>
     */
    protected array $handlers = [];

    public function __construct(public Invoker $invoker)
    {
    }

    public function register(string $commandClass, mixed $commandHandler): void
    {
        $this->handlers[$commandClass] = $commandHandler;
    }

    public function addMiddleware(string $name, mixed $callable, int $priority = 0): void
    {
        ($this->middlewares[$name] ??= new CallableMiddleware($this->invoker))->add($callable, $priority);
    }

    public function handle(object $command): mixed
    {
        // Work on a copy so the registered middleware stack is not mutated by the appended handler.
        $stack = isset($this->middlewares[$command::class])
            ? clone $this->middlewares[$command::class]
            : new CallableMiddleware($this->invoker);

        $stack->add($this->getCallableForCommand($command), PHP_INT_MIN); // executed last

        return $stack->run($command);
    }

    protected function getCallableForCommand(object $commandInstance): mixed
    {
        $commandClass = $commandInstance::class;
        $handler      = $this->handlers[$commandClass] ?? preg_replace('/(.+)Command$/', '$1Handler', $commandClass);

        if (is_string($handler)) {
            if (! class_exists($handler)) {
                // maybe the handler is something like `SomeClass::method` or `SomeClass@method`
                return $handler;
            }
            if (method_exists($handler, 'handle')) {
                return $handler . '@handle';
            }
            if (method_exists($handler, '__invoke')) {
                return $handler . '@__invoke';
            }
            throw new \RuntimeException('Unable to determine the command handler');
        }

        return $handler;
    }
}
