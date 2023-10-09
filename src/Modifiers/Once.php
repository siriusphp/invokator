<?php

declare(strict_types=1);

namespace Sirius\StackRunner\Modifiers;

use Sirius\StackRunner\Invoker;
use Sirius\StackRunner\InvokerAwareInterface;

class Once implements InvokerAwareInterface
{
    protected Invoker $invoker;

    protected mixed $result = null;

    protected bool $has_run = false;

    public function __construct(public mixed $callable)
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
        if ($this->has_run) {
            return $this->result;
        }

        $this->result = $this->invoker->invoke($this->callable, ...$params);
        $this->has_run = true;

        return $this->result;
    }
}
