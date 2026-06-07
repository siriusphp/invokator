<?php

declare(strict_types=1);

namespace Sirius\Invokator;

/**
 * A self-contained collection of callables that knows how to run itself.
 *
 * Implementations own a {@see CallableCollection} plus the {@see Invoker}, expose `add()`
 * to register callables and `run()` to execute them according to their own strategy
 * (pipeline, middleware, filter, action...).
 */
interface RunnableCallables
{
    public function run(mixed ...$args): mixed;
}
