<?php

declare(strict_types=1);

namespace Sirius\Invokator\Modifiers;

use Sirius\Invokator\ArgumentReference;
use Sirius\Invokator\Invoker;
use Sirius\Invokator\InvokerAwareInterface;
use Sirius\Invokator\InvokerReference;

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
