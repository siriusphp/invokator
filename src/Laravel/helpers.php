<?php

declare(strict_types=1);

use Sirius\Invokator\Laravel\InvokatorManager;
use Sirius\Invokator\Laravel\Registrar;

if (! function_exists('do_pipeline')) {
    /**
     * Define a pipeline (identifier only, returns a {@see Registrar}) or run it (with args).
     */
    function do_pipeline(string $id, mixed ...$args): mixed
    {
        return app(InvokatorManager::class)->pipeline($id, ...$args);
    }
}

if (! function_exists('do_action')) {
    /**
     * Define an action (identifier only, returns a {@see Registrar}) or run it (with args).
     */
    function do_action(string $id, mixed ...$args): mixed
    {
        return app(InvokatorManager::class)->action($id, ...$args);
    }
}

if (! function_exists('do_filter')) {
    /**
     * Define a filter (identifier only, returns a {@see Registrar}) or run it (with args),
     * returning the filtered value.
     */
    function do_filter(string $id, mixed ...$args): mixed
    {
        return app(InvokatorManager::class)->filter($id, ...$args);
    }
}

if (! function_exists('do_middleware')) {
    /**
     * Define a middleware stack (identifier only, returns a {@see Registrar}) or run it (with args).
     */
    function do_middleware(string $id, mixed ...$args): mixed
    {
        return app(InvokatorManager::class)->middleware($id, ...$args);
    }
}

if (! function_exists('do_event')) {
    /**
     * Dispatch a PSR-14 event object to its subscribed listeners.
     */
    function do_event(object $event): object
    {
        return app(InvokatorManager::class)->dispatch($event);
    }
}
