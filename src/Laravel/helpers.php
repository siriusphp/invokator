<?php

declare(strict_types=1);

use Sirius\Invokator\Invokator;

if (! function_exists('do_pipeline')) {
    /**
     * Reference a pipeline (identifier only, returns the pipeline to define callables on) or
     * run it (with args), returning the pipeline's result.
     */
    function do_pipeline(string $id, mixed ...$args): mixed
    {
        $pipeline = app(Invokator::class)->pipeline($id);

        return $args === [] ? $pipeline : $pipeline->run(...$args);
    }
}

if (! function_exists('do_action')) {
    /**
     * Reference an action (identifier only, returns the action to define callables on) or
     * run it (with args) for its side effects.
     */
    function do_action(string $id, mixed ...$args): mixed
    {
        $action = app(Invokator::class)->action($id);

        return $args === [] ? $action : $action->run(...$args);
    }
}

if (! function_exists('do_filter')) {
    /**
     * Reference a filter (identifier only, returns the filter to define callables on) or
     * run it (with args), returning the filtered value.
     */
    function do_filter(string $id, mixed ...$args): mixed
    {
        $filter = app(Invokator::class)->filter($id);

        return $args === [] ? $filter : $filter->run(...$args);
    }
}

if (! function_exists('do_middleware')) {
    /**
     * Reference a middleware stack (identifier only, returns the stack to define callables on)
     * or run it (with args).
     */
    function do_middleware(string $id, mixed ...$args): mixed
    {
        $middleware = app(Invokator::class)->middleware($id);

        return $args === [] ? $middleware : $middleware->run(...$args);
    }
}

if (! function_exists('do_event')) {
    /**
     * Dispatch a PSR-14 event object to its subscribed listeners.
     */
    function do_event(object $event): object
    {
        return app(Invokator::class)->dispatch($event);
    }
}
