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

use Psr\Container\ContainerInterface;use Sirius\Invokator\InvalidCallableException;
use Sirius\Invokator\CallableCollection;
use Sirius\Invokator\Invoker as BaseInvoker;
use Sirius\Invokator\Processors\MiddlewareProcessor;

class Invoker extends BaseInvoker
{
    private MiddlewareProcessor $middlewareProcessor;
    
    public function __construct(ContainerInterface $container) {
        parent::__construct($container);
        $this->middlewareProcessor =  new MiddlewareProcessor($this);
    }
    
    public function addMiddleware(string $name, mixed $callable, int $priority = 0) {
        $collection = $this->middlewareProcessor->get($name);
        if ($collection->isEmpty()) {
            // this adds the original callable as the last item in the callables
            $collection->add($name, $name, PHP_INT_MIN);
        }
        $this->middlewareProcessor->add($name, $callable, $priority);
    }
    
    public function invoke(mixed $callable,...$params) : mixed{
        $callables = $this->middlewareProcessor->get($name);
        if ( ! $callables->isEmpty()) {
            return $this->middlewareProcessor->processCollection($callables, ...$params);
        }
        
        return parent::invoke($callable,$params);
    }
}
```

The 3rd-party module or somewhere in your service providers you can do

```php
use function Sirius\Invokator\with_arguments;

class SomeServiceProvider {
    public function boot() {
        $this->invoker->add('ListProducts@execute', with_arguments('CacheMiddleware::cache', arg(0), 10 * 60)); // cache for 10 minutes
        $this->invoker->add('ListProducts@create', with_arguments('CacheMiddleware::forget', arg(0)); // cache for 10 minutes
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
