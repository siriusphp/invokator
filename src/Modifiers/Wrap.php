<?php

declare(strict_types=1);

namespace Sirius\Invokator\Modifiers;

use Sirius\Invokator\Invoker;
use Sirius\Invokator\InvokerAwareInterface;

class Wrap implements InvokerAwareInterface
{
    protected Invoker $invoker;

    /**
     * @param callable $wrapperCallback
     */
    public function __construct(public readonly mixed $callable, public readonly mixed $wrapperCallback)
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
        $next = fn (): mixed => $this->invoker->invoke($this->callable, ...$params);

        return call_user_func($this->wrapperCallback, $next);
    }
}
