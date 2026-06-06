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
(Laravel's container implements `Psr\Container\ContainerInterface`), and registers the
processors and the event dispatcher as **singletons** so that registrations made during
boot persist for the whole request.

## Defining vs. running

Every pattern uses the same overloaded call: pass **only the identifier** to get a builder
you can `->add()` callables to, or pass **extra arguments** to run it.

```php
use Sirius\Invokator\Laravel\Facades\Invokator;

// define
Invokator::pipeline('process-order')
    ->add(fn ($order) => /* ... */ $order)
    ->add(OrderNormalizer::class . '@handle');

// run
$result = Invokator::pipeline('process-order', $order);
```

`add()` accepts an optional priority (higher runs first) and, for actions and filters, an
optional argument limit: `->add($callable, $priority = 0, $argumentsLimit = 1)`.

> Running a pattern with **no** arguments is not expressible through this overload, because
> `Invokator::pipeline('id')` returns the builder. For that rare case resolve the processor
> directly, e.g. `app(\Sirius\Invokator\Processors\PipelineProcessor::class)->process('id')`.

## The `Invokator` facade

### Pipelines, actions, filters, middlewares

```php
// Pipeline — the result of each callable is the only argument to the next
Invokator::pipeline('slugify')
    ->add(fn ($title) => trim($title))
    ->add(fn ($title) => strtolower($title));
Invokator::pipeline('slugify', '  Hello World  '); // "hello world"

// Filter — transform a value (extra arguments are kept for every callable)
Invokator::filter('price')->add(fn ($amount) => $amount * 1.2);
Invokator::filter('price', 100); // 120

// Action — run callables for their side effects; returns null
Invokator::action('analytics')->add(fn ($user) => Analytics::track($user));
Invokator::action('analytics', $user);

// Middleware — each callable receives the arguments plus a $next callback
Invokator::middleware('http')
    ->add(fn ($request, $next) => $next($request))
    ->add(fn ($request, $next) => /* terminal */ $request);
Invokator::middleware('http', $request);
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

## Helper functions

The same operations are available as global helpers, prefixed with `do_` to avoid clashing
with Laravel's built-in `event()` and `action()` helpers. They are loaded by the service
provider, so they only exist inside a Laravel application.

```php
do_pipeline('process-order')->add(/* ... */);
do_pipeline('process-order', $order);

do_filter('price', 100);
do_action('analytics', $user);
do_middleware('http', $request);

do_event(new OrderPlaced($order));
```

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
