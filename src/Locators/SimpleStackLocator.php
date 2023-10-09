<?php

declare(strict_types=1);

namespace Sirius\StackRunner\Locators;

use Sirius\StackRunner\Invoker;
use Sirius\StackRunner\Stack;
use Sirius\StackRunner\StackRegistryInterface;
use Sirius\StackRunner\StackRunnerInterface;

class SimpleStackLocator implements StackRegistryInterface, StackRunnerInterface
{
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

    public function add(string $name, $callable, int $priority = 0): Stack
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

    public function process(string $name, ...$params)
    {
        return $this->processStack($this->getCopy($name), ...$params);
    }

    public function processStack(Stack $stack, ...$params)
    {
        $nextCallable = $stack->extract();

        while ($nextCallable !== null) {
            $this->invoker->invoke($nextCallable, ...$params);
            $nextCallable = $stack->isEmpty() ? null : $stack->extract();
        }
    }


}
