<?php

declare(strict_types=1);

namespace Sirius\Invokator\Processors;

use Sirius\Invokator\InvalidCallableException;
use Sirius\Invokator\CallableCollection;

class MiddlewareProcessor extends SimpleCallablesProcessor
{
    /**
     * @param array<mixed> $params
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws InvalidCallableException
     */
    public function processCollection(CallableCollection $stack, ...$params): mixed
    {
        $result       = null;
        $nextCallable = $stack->extract();

        while ($nextCallable !== null) {
            if ($stack->isEmpty()) {
                $response = $this->invoker->invoke($nextCallable, ...$params);
            } else {
                $next          = fn ($result) => $this->processCollection($stack, ...$params);
                $paramsForNext = [...$params, $next];
                $response      = $this->invoker->invoke($nextCallable, ...$paramsForNext);
            }

            $result = $response;

            $nextCallable = $stack->isEmpty() ? null : $stack->extract();
        }

        return $result;
    }

}
