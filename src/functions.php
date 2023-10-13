<?php

declare(strict_types=1);

namespace Sirius\Invokator;

use Sirius\Invokator\Modifiers\LimitArguments;
use Sirius\Invokator\Modifiers\Once;
use Sirius\Invokator\Modifiers\ResolveArguments;
use Sirius\Invokator\Modifiers\WithArguments;
use Sirius\Invokator\Modifiers\Wrap;

if (! function_exists('Sirius\Invokator\ref')) {
    function ref(string $ref): InvokerReference
    {
        return new InvokerReference($ref);
    }
}

if (! function_exists('Sirius\Invokator\arg')) {
    function arg(int $ref): ArgumentReference
    {
        return new ArgumentReference($ref);
    }
}

if (! function_exists('Sirius\Invokator\resolve')) {
    /**
     * @param array<string, mixed> $params
     */
    function resolve(mixed $callable, array $params = []): ResolveArguments
    {
        return new ResolveArguments($callable, $params);
    }
}

if (! function_exists('Sirius\Invokator\result_of')) {
    /**
     * @param array<mixed> $params
     */
    function result_of(mixed $callable, array $params = []): InvokerResult
    {
        return new InvokerResult($callable, $params);
    }
}

if (! function_exists('Sirius\Invokator\with_arguments')) {
    /**
     * @param array<mixed> $arguments
     */
    function with_arguments(mixed $callable, array $arguments): WithArguments
    {
        return new WithArguments($callable, $arguments);
    }
}

if (! function_exists('Sirius\Invokator\limit_arguments')) {
    function limit_arguments(mixed $callable, int $argumentsLimit): LimitArguments
    {
        return new LimitArguments($callable, $argumentsLimit);
    }
}

if (! function_exists('Sirius\Invokator\once')) {
    function once(mixed $callable): Once
    {
        return new Once($callable);
    }
}

if (! function_exists('Sirius\Invokator\wrap')) {
    /**
     * @param callable $wrapperCallback
     */
    function wrap(mixed $callable, $wrapperCallback): Wrap
    {
        return new Wrap($callable, $wrapperCallback);
    }
}
