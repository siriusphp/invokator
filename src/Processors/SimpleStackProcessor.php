<?php

declare(strict_types=1);

namespace Sirius\StackRunner\Processors;

use Sirius\StackRunner\Invoker;
use Sirius\StackRunner\Stack;
use Sirius\StackRunner\StackRegistryInterface;
use Sirius\StackRunner\StackRunnerInterface;

class SimpleStackProcessor implements StackRegistryInterface, StackRunnerInterface
{
    /**
     * @var array<Stack>
     */
    protected $registry = [];

    public function __construct(public Invoker $invoker)
    {
    }

    public function get(string $name): Stack
    {
        if (! isset($this->registry[$name])) {
            $this->registry[$name] = $this->newStack();
        }

        return $this->registry[$name];
    }

    public function add(string $name, mixed $callable, int $priority = 0): Stack
    {
        return $this->get($name)->add($callable, $priority);
    }

    protected function newStack(): Stack
    {
        return new Stack();
    }

    protected function getCopy(string $name): Stack
    {
        return clone($this->get($name));
    }

    /**
     * @param array<mixed> $params
     */
    public function process(string $name, ...$params): mixed
    {
        return $this->processStack($this->getCopy($name), ...$params);
    }

    /**
     * @param array<mixed> $params
     */
    public function processStack(Stack $stack, ...$params): mixed
    {
        $nextCallable = $stack->extract();

        while ($nextCallable !== null) {
            $this->invoker->invoke($nextCallable, ...$params);
            $nextCallable = $stack->isEmpty() ? null : $stack->extract();
        }

        return null;
    }


}