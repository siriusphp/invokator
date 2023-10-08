<?php
declare(strict_types=1);

namespace Sirius\StackRunner\Locators;

use Sirius\StackRunner\Stack;

class MiddlewareLocator extends SimpleStackLocator
{
    public function processStack(Stack $stack, ...$params)
    {
        $result       = null;
        $nextCallable = $stack->extract();

        while ($nextCallable !== null) {
            if ($stack->isEmpty()) {
                $response = $this->invoker->invoke($nextCallable, ...$params);
            } else {
                $next          = fn($result) => $this->processStack($stack, ...$params);
                $paramsForNext = [...$params, $next];
                $response      = $this->invoker->invoke($nextCallable, ...$paramsForNext);
            }

            $result = $response;

            $nextCallable = $stack->isEmpty() ? null : $stack->extract();
        }

        return $result;
    }

}
