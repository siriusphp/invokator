<?php

declare(strict_types=1);

namespace Sirius\Invokator\Modifiers;

use Sirius\Invokator\Invoker;
use Sirius\Invokator\InvokerAwareInterface;

class Once implements InvokerAwareInterface
{
    protected Invoker $invoker;

    protected mixed $result = null;

    protected bool $hasRun = false;

    public function __construct(public readonly mixed $callable)
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
        if ($this->hasRun) {
            return $this->result;
        }

        $this->result = $this->invoker->invoke($this->callable, ...$params);
        $this->hasRun = true;

        return $this->result;
    }
}
