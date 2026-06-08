---
title: Using Sirius\Invokator with Laravel
---

# Laravel integration

Sirius\Invokator ships an optional Laravel bridge that exposes the library through
idiomatic Laravel surfaces: a service provider, an `Invokator` facade, a set of `do_*`
helper functions and a Blade directive. The core library stays framework-agnostic — the
bridge only loads inside a Laravel application.

## Installation

```bash
composer require siriusphp/invokator
```

The package is auto-discovered, so there is nothing to register manually. Laravel reads the
`extra.laravel` section of `composer.json` and registers:

- the `Sirius\Invokator\Laravel\SiriusInvokatorServiceProvider` service provider, and
- the `Invokator` facade alias.

The service provider injects Laravel's container into the `Invoker` as its PSR-11 container
(Laravel's container implements `Psr\Container\ContainerInterface`). It registers the
`Sirius\Invokator\Invokator` class as a **singleton** (aliased `invokator`) so that
registrations made during boot persist for the whole request, and binds the `Dispatcher`
and the `CommandBus` from it so they remain injectable on their own.

## Defining vs. running

The facade is the explicit core API: every pattern method returns the runnable. You
register callables on it with `->add()` (or in bulk by passing them to the method) and
execute it with `->run()`.

```php
use Sirius\Invokator\Laravel\Facades\Invokator;

// define
Invokator::pipeline('process-order')
    ->add(fn ($order) => /* ... */ $order)
    ->add(OrderNormalizer::class . '@handle');

// or define in bulk
Invokator::pipeline('process-order', fn ($order) => $order, OrderNormalizer::class . '@handle');

// run
$result = Invokator::pipeline('process-order')->run($order);
```

> **Important:** extra arguments passed to `pipeline()`/`filter()`/`action()`/`middleware()`
> now **register** callables — they do **not** auto-run (this is a change from the old
> facade). Always execute with `->run(...)`. Running with no arguments is simply
> `Invokator::pipeline('id')->run()`.

`add()` accepts an optional priority (higher runs first) and, for actions and filters, an
optional argument limit: `->add($callable, $priority = 0, $argumentsLimit = 1)`.

## The `Invokator` facade

### Pipelines, actions, filters, middlewares

```php
// Pipeline — the result of each callable is the only argument to the next
Invokator::pipeline('slugify')
    ->add(fn ($title) => trim($title))
    ->add(fn ($title) => strtolower($title));
Invokator::pipeline('slugify')->run('  Hello World  '); // "hello world"

// Filter — transform a value (extra arguments are kept for every callable)
Invokator::filter('price')->add(fn ($amount) => $amount * 1.2);
Invokator::filter('price')->run(100); // 120

// Action — run callables for their side effects; returns null
Invokator::action('analytics')->add(fn ($user) => Analytics::track($user));
Invokator::action('analytics')->run($user);

// Middleware — each callable receives the arguments plus a $next callback
Invokator::middleware('http')
    ->add(fn ($request, $next) => $next($request))
    ->add(fn ($request, $next) => /* terminal */ $request);
Invokator::middleware('http')->run($request);
```

### Events (PSR-14)

Events keep PSR-14 semantics: you dispatch an event **object** and the listener key is taken
from its class name (or `HasEventName::getEventName()`).

```php
// subscribe
Invokator::event(OrderPlaced::class)->add(function (OrderPlaced $event) {
    // ...
});

// dispatch
Invokator::dispatch(new OrderPlaced($order));
```

Events that implement `Psr\EventDispatcher\StoppableEventInterface` (for instance via the
`Sirius\Invokator\Event\Stoppable` trait) stop propagation as usual.

### Commands

```php
Invokator::command(CreateProductCommand::class)
    ->add('CommandMiddleware@execute', 100)
    ->handledBy(CreateProductHandler::class);

Invokator::handle(new CreateProductCommand(/* ... */));
```

## Helper functions

The same operations are available as global helpers, prefixed with `do_` to avoid clashing
with Laravel's built-in `event()` and `action()` helpers. They are loaded by the service
provider, so they only exist inside a Laravel application.

Unlike the facade, the `do_*` helpers keep the **WordPress-style "run on extra arguments"**
convention: called with **only the identifier** they return the runnable so you can define
callables on it; called **with arguments** they run it.

```php
// define
do_pipeline('process-order')->add(/* ... */);
// run
do_pipeline('process-order', $order);

do_filter('price', 100);     // returns the filtered value
do_action('analytics', $user); // runs the side effects
do_middleware('http', $request); // runs the stack

do_event(new OrderPlaced($order)); // dispatches the event
```

> This asymmetry is intentional: the facade/core API is explicit (`->run()`), while the
> `do_*` helpers run on extra arguments (the WordPress convention) so the Blade directive
> and WP-style usage work naturally.

## Blade

Use the `@do_action` directive to run an action from a template (it emits nothing), and the
`do_filter()` helper inside an echo to print a filtered value:

```blade
<html>
<head>
    @do_action('html-head', $page)
    <title>{{ do_filter('page-title', $title) }}</title>
</head>
...
</html>
```

## Resolving callables

Because the `Invoker` is wired to Laravel's container, string callables are resolved through
it. The following all work:

- **Closures** — `fn ($x) => ...`.
- **`Service@method`** — the `Service` is resolved from the container *with dependency
  injection*, then `method` is called.
- **Bound service ids / invokable services** registered in the container.
- **Plain function names** (`'trim'`) and **`Class::method`** static strings — used directly
  when they are not bound in the container.

[Next: Upgrading to 3.0](9_upgrading_to_3.md)
