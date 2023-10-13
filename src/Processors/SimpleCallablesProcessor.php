<?php

declare(strict_types=1);

namespace Sirius\Invokator\Processors;

use Sirius\Invokator\Invoker;
use Sirius\Invokator\CallableCollection;
use Sirius\Invokator\CallablesRegistryInterface;
use Sirius\Invokator\InvokatorInterface;

class SimpleCallablesProcessor implements CallablesRegistryInterface, InvokatorInterface
{
    /**
     * @var array<CallableCollection>
     */
    protected $registry = [];

    public function __construct(public Invoker $invoker)
    {
    }

    public function get(string $name): CallableCollection
    {
        if (! isset($this->registry[$name])) {
            $this->registry[$name] = $this->newStack();
        }

        return $this->registry[$name];
    }

    public function add(string $name, mixed $callable, int $priority = 0): CallableCollection
    {
        return $this->get($name)->add($callable, $priority);
    }

    protected function newStack(): CallableCollection
    {
        return new CallableCollection();
    }

    protected function getCopy(string $name): CallableCollection
    {
        return clone($this->get($name));
    }

    /**
     * @param array<mixed> $params
     */
    public function process(string $name, ...$params): mixed
    {
        return $this->processCollection($this->getCopy($name), ...$params);
    }

    /**
     * @param array<mixed> $params
     */
    public function processCollection(CallableCollection $stack, ...$params): mixed
    {
        $nextCallable = $stack->extract();
        while ($nextCallable !== null) {
            $this->invoker->invoke($nextCallable, ...$params);
            $nextCallable = $stack->isEmpty() ? null : $stack->extract();
        }

        return null;
    }


}
