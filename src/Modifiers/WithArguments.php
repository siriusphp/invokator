<?php

declare(strict_types=1);

namespace Sirius\StackRunner\Modifiers;

use Sirius\StackRunner\ArgumentReference;
use Sirius\StackRunner\Invoker;
use Sirius\StackRunner\InvokerAwareInterface;
use Sirius\StackRunner\InvokerReference;

class WithArguments implements InvokerAwareInterface
{
    protected Invoker $invoker;

    /**
     * @param array<mixed> $arguments
     */
    public function __construct(public mixed $callable, public array $arguments)
    {
    }

    public function setInvoker(Invoker $invoker): void
    {
        $this->invoker = $invoker;
    }

    /**
     * @param array<mixed> $params
     */
    public function __invoke(...$params): mixed
    {
        $passedArgs = $this->getPassedArguments($params);

        return $this->invoker->invoke($this->callable, ...$passedArgs);
    }

    /**
     * @param array<mixed> $params
     * @return array<mixed>
     */
    private function getPassedArguments(array $params = []): array
    {
        $pass = [];
        foreach ($this->arguments as $arg) {
            if ($arg instanceof ArgumentReference) {
                $pass[] = $params[$arg->reference] ?? null;
                continue;
            }
            $pass[] = $arg;
        }

        return $pass;
    }
}
