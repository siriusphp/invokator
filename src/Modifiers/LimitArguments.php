<?php

declare(strict_types=1);

namespace Sirius\Invokator\Modifiers;

use Sirius\Invokator\Invoker;
use Sirius\Invokator\InvokerAwareInterface;

class LimitArguments implements InvokerAwareInterface
{
    protected Invoker $invoker;

    public function __construct(public mixed $callable, public int $argumentsLimit)
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
        $passedParams = array_slice($params, 0, $this->argumentsLimit);

        return $this->invoker->invoke($this->callable, ...$passedParams);
    }
}
