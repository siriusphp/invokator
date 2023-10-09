<?php

declare(strict_types=1);

namespace Sirius\StackRunner\Locators;

use Sirius\StackRunner\InvalidCallableException;
use Sirius\StackRunner\Stack;

class MiddlewareLocator extends SimpleStackLocator
{
    /**
     * @param array<mixed> $params
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws InvalidCallableException
     */
    public function processStack(Stack $stack, ...$params): mixed
    {
        $result       = null;
        $nextCallable = $stack->extract();

        while ($nextCallable !== null) {
            if ($stack->isEmpty()) {
                $response = $this->invoker->invoke($nextCallable, ...$params);
            } else {
                $next          = fn ($result) => $this->processStack($stack, ...$params);
                $paramsForNext = [...$params, $next];
                $response      = $this->invoker->invoke($nextCallable, ...$paramsForNext);
            }

            $result = $response;

            $nextCallable = $stack->isEmpty() ? null : $stack->extract();
        }

        return $result;
    }

}
