<?php

declare(strict_types=1);

namespace Sirius\StackRunner\Modifiers;

use Sirius\StackRunner\Invoker;
use Sirius\StackRunner\InvokerAwareInterface;

class Wrap implements InvokerAwareInterface
{
    protected Invoker $invoker;

    /**
     * @param mixed $callable
     * @param callable $wrapperCallback
     */
    public function __construct(public mixed $callable, public $wrapperCallback)
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
        $next = fn () => $this->invoker->invoke($this->callable, ...$params);

        return call_user_func($this->wrapperCallback, $next);
    }
}
