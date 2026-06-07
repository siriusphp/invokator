---
title: Upgrading Sirius\Invokator from 2.x to 3.0
---

# Upgrading to 3.0

Version 3.0 splits the old "processor" classes — which were both a registry of
name-keyed collections **and** the executor — into two clear responsibilities:
self-contained **runnable callable stacks** and a unified **registry**.

## 1. The `Sirius\Invokator\Processors\*` classes are removed

The whole `Sirius\Invokator\Processors` namespace is gone. Replace each processor
with the matching runnable from `Sirius\Invokator\Callables`:

| Removed | Replacement |
| --- | --- |
| `PipelineProcessor` | `CallablePipeline` |
| `MiddlewareProcessor` | `CallableMiddleware` |
| `FiltersProcessor` | `CallableFilter` |
| `ActionsProcessor` | `CallableAction` |
| `SimpleCallablesProcessor` | `CallableAction` with `argumentsLimit: null` |
| `Processors\CommandBus` | `Callables\CommandBus` |

A runnable **owns its own callables** and is executed with `run(...)`:

```php
// before (2.x)
$processor = new PipelineProcessor($invoker);
$processor->add('slug', fn ($t) => trim($t));
$processor->add('slug', 'strtolower');
$processor->process('slug', '  Hello  ');

// after (3.0)
$pipeline = new CallablePipeline($invoker);
$pipeline->add(fn ($t) => trim($t))->add('strtolower');
$pipeline->run('  Hello  ');
```

## 2. `get()`, `process()` and `processCollection()` are gone

There is no longer a name-keyed `process('id', ...)` / `get('id')->add(...)` API on
the runners. A runnable holds a single collection and you `add()` to it and `run()`
it directly. The interfaces `InvokatorInterface` and `CallablesRegistryInterface`
have been removed.

## 3. The new `Sirius\Invokator\Invokator` registry

If you still want to register stacks by identifier, use the new framework-agnostic
`Invokator` class, constructed from a single `Invoker`. Its `pipeline()`,
`middleware()`, `filter()`, `action()`, `event()` and `command()` methods return the
(cached-per-id) runnable, and trailing callables passed to them are registered in
bulk at the default priority.

```php
$invokator = new Invokator(new Invoker($psr11Container));

// register in bulk
$invokator->pipeline('slug', fn ($t) => trim($t), 'strtolower');
// register with a per-callable priority
$invokator->pipeline('slug')->add('SlugService@finish', 10);
// run
$invokator->pipeline('slug')->run('  Hello  ');
```

It also exposes `dispatch()`/`handle()` convenience methods and `dispatcher()`/
`commandBus()` accessors.

## 4. The simple collection is now `CallableAction`

`SimpleCallablesProcessor` (every callable gets the same arguments, results ignored)
is replaced by `CallableAction` with `argumentsLimit: null`:

```php
// before (2.x)
$processor = new SimpleCallablesProcessor($invoker);
$processor->add('log', 'FileLogger@log');
$processor->process('log', $severity, $message, $context);

// after (3.0)
$action = new CallableAction($invoker);
$action->add('FileLogger@log', 0, null); // argumentsLimit: null => every argument is passed
$action->run($severity, $message, $context);
```

The default `argumentsLimit` of `1` gives the Wordpress action behaviour.

## 5. Laravel

The facade methods now **return the runnable**; you execute it with `->run(...)`.
Extra arguments passed to `pipeline()`/`filter()`/`action()`/`middleware()` now
**register** callables — they no longer auto-run.

```php
// before (2.x): extra arguments ran the pattern
Invokator::filter('price', 100); // ran the filter

// after (3.0): extra arguments register callables; you run explicitly
Invokator::filter('price')->add(fn ($a) => $a * 1.2);
Invokator::filter('price')->run(100); // 120
```

The `do_*` helpers keep the WordPress-style "run on extra arguments" convention
(`do_filter('price', 100)` returns the filtered value, `do_action('tag', ...$args)`
runs the side effects, etc.), so the Blade directive and WP-style usage still work.

`InvokatorManager` and `Laravel\Registrar` are removed in favour of the core
`Invokator`, which the service provider registers as a singleton.

```bash
composer require siriusphp/invokator:^3.0
```
