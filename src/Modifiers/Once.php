<?php
declare(strict_types=1);

namespace Sirius\StackRunner\Modifiers;

use Sirius\StackRunner\Invoker;
use Sirius\StackRunner\InvokerAwareInterface;

class Once implements InvokerAwareInterface
{
    protected Invoker $invoker;

    protected $result = null;

    protected $has_run = false;

    public function __construct(public $callable)
    {
    }

    public function setInvoker(Invoker $invoker)
    {
        $this->invoker = $invoker;
    }

    public function __invoke(...$params)
    {
        if ($this->has_run) {
            return $this->result;
        }

        $this->result = $this->invoker->invoke($this->callable, ...$params);
        $this->has_run = true;

        return $this->result;
    }
}
