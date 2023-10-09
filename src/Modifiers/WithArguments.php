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

    public function __construct(public $callable, public array $arguments)
    {
    }

    public function setInvoker(Invoker $invoker)
    {
        $this->invoker = $invoker;
    }

    public function __invoke(...$params)
    {
        $passedArgs = $this->getPassedArguments($params);

        return $this->invoker->invoke($this->callable, ...$passedArgs);
    }

    private function getPassedArguments($params)
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
