<?php

declare(strict_types=1);

namespace Sirius\StackRunner\Modifiers;

use Sirius\StackRunner\Invoker;
use Sirius\StackRunner\InvokerAwareInterface;

class LimitArguments implements InvokerAwareInterface
{
    protected Invoker $invoker;

    public function __construct(public $callable, public int $argumentsLimit)
    {
    }

    public function setInvoker(Invoker $invoker)
    {
        $this->invoker = $invoker;
    }

    public function __invoke(...$params)
    {
        $passedParams = array_slice($params, 0, $this->argumentsLimit);

        return $this->invoker->invoke($this->callable, ...$passedParams);
    }
}
