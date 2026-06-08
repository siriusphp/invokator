---
title: The Invokator registry
---

# The Invokator registry

The runnable callable stacks are self-contained, but in a real application you usually want to
**define** them in one place (a service provider, a bootstrap file, a plugin) and **run** them
somewhere else. The `Sirius\Invokator\Invokator` class is the framework-agnostic registry that
holds the runners by identifier and is the unified entry point to every pattern in the library.

It is built from a single [`Invoker`](4_the_invoker.md):

```php
use Sirius\Invokator\Invokator;
use Sirius\Invokator\Invoker;

$invokator = new Invokator(new Invoker($psr11Container));
```

> This `$invokator` is the object used throughout the rest of the pattern pages. The constructor
> also accepts an optional PSR-14 `Dispatcher` and a `CommandBus` if you want to share existing
> instances; otherwise it creates its own.

## Defining vs. running

Each pattern method returns the runner. You register callables on it with `->add()` (or in bulk by
passing them straight to the method) and execute it with `->run()`:

```php
// define
$invokator->pipeline('slug')
          ->add(fn ($t) => trim($t))
          ->add('strtolower');

// define in bulk (callables passed to the method, registered at the default priority)
$invokator->pipeline('slug', fn ($t) => trim($t), 'strtolower');

// run, anywhere, as many times as you want
$invokator->pipeline('slug')->run('  Hello  '); // "hello"
```

The stack patterns are **cached per identifier**: calling `pipeline('slug')` again returns the
*same* runner, so registrations accumulate across calls. `->add()` also takes a priority (higher
runs first) and, for actions and filters, an argument limit: `->add($callable, $priority = 0, $argumentsLimit = 1)`.

## The API

| Method | Returns | Purpose |
| --- | --- | --- |
| `pipeline($id, ...$callables)` | [`CallablePipeline`](2_2_pipelines.md) | result of each callable feeds the next |
| `middleware($id, ...$callables)` | [`CallableMiddleware`](2_3_middlewares.md) | each callable gets a `$next` to continue the stack |
| `filter($id, ...$callables)` | [`CallableFilter`](2_5_wordpress_filters.md) | thread the first argument through the callables |
| `action($id, ...$callables)` | [`CallableAction`](2_4_wordpress_actions.md) | run callables for their side effects |
| `event($eventName, ...$listeners)` | [`CallableEvent`](2_3_event_dispatcher.md) | subscribe listeners to an event |
| `command($commandClass, ...$middleware)` | [`CallableCommand`](2_3_command_bus.md) | wrap a command in middleware |

Events and commands are dispatched with the convenience methods (equivalent to `->run()` on the
returned wrapper):

```php
$invokator->event(OrderPlaced::class)->add(fn (OrderPlaced $e) => /* ... */ null);
$invokator->dispatch(new OrderPlaced($order));

$invokator->command(CreateProduct::class)->add('LogMiddleware@handle');
$invokator->handle(new CreateProduct(/* ... */));
```

The underlying PSR-14 dispatcher and the command bus are reachable through `$invokator->dispatcher()`
and `$invokator->commandBus()` if you need them directly.

[Next: Simple callable collections](2_1_simple_collection.md)
