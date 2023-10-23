<?php

declare(strict_types=1);

namespace Sirius\Invokator\Processors;

use Sirius\Invokator\InvalidCallableException;
use Sirius\Invokator\CallableCollection;

class CommandBus extends MiddlewareProcessor
{
    /**
     * We store the handlers separately, so we don't have to force the handlers
     * to be defined before the middleware.
     * The handlers will be added to the middleware before being executed
     * @see handle()
     * @var array<string, mixed> $handlers
     */
    protected array $handlers = [];

    public function register(string $commandClass, mixed $commandHandler): void
    {
        $this->handlers[$commandClass] = $commandHandler;
    }

    public function addMiddleware(string $name, mixed $callable, int $priority = 0): void
    {
        parent::add($name, $callable, $priority);
    }

    public function handle(object $command): mixed
    {
        $callableCollection = $this->getCopy(get_class($command));
        $callableCollection->add($this->getCallableForCommand($command), PHP_INT_MIN); // to be executed at the end

        $result       = null;
        $nextCallable = $callableCollection->extract();
        while ($nextCallable !== null) {
            if ($callableCollection->isEmpty()) {
                $response = $this->invoker->invoke($nextCallable, $command);
            } else {
                $next     = fn($result) => $this->processCollection($callableCollection, $command);
                $response = $this->invoker->invoke($nextCallable, $command, $next);
            }

            $result = $response;

            $nextCallable = $callableCollection->isEmpty() ? null : $callableCollection->extract();
        }

        return $result;
    }

    public function process(string $name, ...$params): mixed
    {
        throw new \BadMethodCallException('You should not call the process() method on the command bus. Use handle() instead!');
    }

    protected function getCallableForCommand(object $commandInstance): mixed
    {
        $commandClass = get_class($commandInstance);
        $handler      = $this->handlers[$commandClass] ?? preg_replace('/(.+)Command$/', '$1Handler', $commandClass);

        if (is_string($handler)) {
            if ( ! class_exists($handler)) {
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
