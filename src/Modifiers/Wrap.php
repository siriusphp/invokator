<?php

declare(strict_types=1);

namespace Sirius\StackRunner\Modifiers;

use Sirius\StackRunner\Invoker;
use Sirius\StackRunner\InvokerAwareInterface;

class Wrap implements InvokerAwareInterface
{
    protected Invoker $invoker;

    public function __construct(public $callable, public $wrapperCallback)
    {
    }

    public function setInvoker(Invoker $invoker)
    {
        $this->invoker = $invoker;
    }

    public function __invoke(...$params)
    {
        $next = fn () => $this->invoker->invoke($this->callable, ...$params);

        return call_user_func($this->wrapperCallback, $next);
    }
}
