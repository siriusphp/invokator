---
title: Automatic middleware
---

# Idea: Automatic middleware

This idea is for when you are building an extensible (via plugins/modules) system you need to allow 3rd party code to intercept the execution of some parts of the system in order overwrite or augment their execution.

It can also be used to simplify. For example, if you want to cache the results of invoking some expensive callables you can do this without polluting the business code with the code related to caching.

The solution involves 

1. Creating a special `Invoker` class which
2. Instead of calling your service directly you invoke them via the `Invoker`

Here's how the code could look like

```php
<?php

declare(strict_types=1);

namespace App\Services;

use Psr\Container\ContainerInterface;
use Sirius\Invokator\Invoker as BaseInvoker;
use Sirius\Invokator\Callables\CallableMiddleware;

class Invoker extends BaseInvoker
{
    /** @var array<string, CallableMiddleware> */
    private array $middlewares = [];

    public function addMiddleware(string $name, mixed $callable, int $priority = 0): void {
        // lazily create a middleware stack for this name
        if ( ! isset($this->middlewares[$name])) {
            $this->middlewares[$name] = new CallableMiddleware($this);
        }
        $this->middlewares[$name]->add($callable, $priority);
    }

    public function invoke(mixed $callable, ...$params): mixed {
        // $callable is the name used to register the middleware (eg: "ListProducts@execute")
        if (is_string($callable) && isset($this->middlewares[$callable])) {
            // run a clone with the real service appended last (the bus-style pattern):
            // the registered middlewares run first and the innermost one calls the service
            $stack = clone $this->middlewares[$callable];
            // the terminal calls parent::invoke() — calling $this->invoke() here would
            // re-detect the middleware for this name and recurse forever
            $stack->add(fn (...$args) => parent::invoke($callable, ...$args), PHP_INT_MIN);

            return $stack->run(...$params);
        }

        return parent::invoke($callable, ...$params);
    }
}
```

The execution method on a `CallableMiddleware` is `run()`. Cloning the stack before appending the terminal keeps the registered middlewares reusable across calls, and the terminal calls `parent::invoke()` (not `$this->invoke()`) so the real service runs without re-entering the middleware lookup for the same name.

The 3rd-party module or somewhere in your service providers you can do

```php
use function Sirius\Invokator\with_arguments;

class SomeServiceProvider {
    public function boot() {
        $this->invoker->addMiddleware('ListProducts@execute', with_arguments('CacheMiddleware::cache', arg(0), 10 * 60)); // cache for 10 minutes
        $this->invoker->addMiddleware('ListProducts@create', with_arguments('CacheMiddleware::forget', arg(0))); // forget on create
    }
}
```


In the app you can use something like

```php
use App\Services\Invoker;

class SomeController {
    public function listProducts(Request $request, Invoker $invoker) {
        return $invoker->invoke('ListProducts@execute', $request->get());
    }
    public function addProduct(Request $request, Invoker $invoker) {
        return $invoker->invoke('ListProducts@create', $request->post());
    }
}
```

Of course this implementation is too simplistic:
1. The `CacheMiddleware` doesn't know what it is caching. Maybe you need to pass in details about what is caching instead of the cache of the lifetime
2. The cache would be better to be invalidated by an event like `ProductCreatedEvent`

[Next: Using Sirius\Invokator with Laravel](7_laravel.md)
