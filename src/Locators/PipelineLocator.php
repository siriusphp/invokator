<?php

declare(strict_types=1);

namespace Sirius\StackRunner\Locators;

use Sirius\StackRunner\DelayedResult;
use Sirius\StackRunner\PipelinePromise;
use Sirius\StackRunner\Stack;

class PipelineLocator extends SimpleStackLocator
{
    public function processStack(Stack $stack, ...$params)
    {
        $result       = null;
        $nextCallable = $stack->extract();

        while ($nextCallable !== null) {
            $result = $this->invoker->invoke($nextCallable, ...$params);

            if ($result instanceof DelayedResult) {
                return new PipelinePromise($result->value, $stack, $params, $result->retryAfter);
            }
            $params = [$result];

            $nextCallable = $stack->isEmpty() ? null : $stack->extract();
        }

        return $result;
    }

    public function resumeStack(Stack $remainingStack, $previousValue, ...$params)
    {

    }

}
