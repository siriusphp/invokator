<?php
declare(strict_types=1);

namespace Sirius\StackRunner;

use Sirius\StackRunner\Modifiers\LimitArguments;
use Sirius\StackRunner\Modifiers\Once;
use Sirius\StackRunner\Modifiers\WithArguments;
use Sirius\StackRunner\Modifiers\Wrap;

if ( ! function_exists('Sirius\StackRunner\ref')) {
    function ref(string $ref): InvokerReference
    {
        return new InvokerReference($ref);
    }
}
if ( ! function_exists('Sirius\StackRunner\result_of')) {
    function result_of($callable, array $params = []): InvokerResult
    {
        return new InvokerResult($callable, $params);
    }
}

if ( ! function_exists('Sirius\StackRunner\with_arguments')) {
    function with_arguments($callable, array $arguments): WithArguments
    {
        return new WithArguments($callable, $arguments);
    }
}

if ( ! function_exists('Sirius\StackRunner\limit_arguments')) {
    function limit_arguments($callable, int $argumentsLimit): LimitArguments
    {
        return new LimitArguments($callable, $argumentsLimit);
    }
}

if ( ! function_exists('Sirius\StackRunner\once')) {
    function once($callable): Once
    {
        return new Once($callable);
    }
}

if ( ! function_exists('Sirius\StackRunner\wrap')) {
    function wrap($callable, $wrapperCallback): Wrap
    {
        return new Wrap($callable, $wrapperCallback);
    }
}
