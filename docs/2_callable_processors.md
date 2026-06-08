---
title: What are callable runners in Sirius\Invokator?
---

# The callable runners (stacks)

A **runnable callable stack** owns a single collection of callables and knows how to execute it. Each runner has the same simple API:

1. `add($callable, $priority)` — register a callable; this returns the runner itself, so calls can be chained
2. `run(...$args)` — execute all the registered callables, the way that particular runner dictates

The runners depend on the [invoker](4_the_invoker.md) to actually execute the callbacks.

You can use a runner on its own:

```php
use Sirius\Invokator\Callables\CallablePipeline;

$pipeline = new CallablePipeline($invoker);
$pipeline->add('trim')->add('ucwords');
$pipeline->run('  hello ');
```

or you can let the `Sirius\Invokator\Invokator` registry hold them by identifier, so the same runner can be defined in one place and executed somewhere else:

```php
$invokator->pipeline('slug')->add(fn ($t) => trim($t))->add('strtolower');
// later on, anywhere
$invokator->pipeline('slug')->run('  Hello  ');
```

The `Sirius\Invokator` library comes with the following runners

1. [simple collection](2_1_simple_collection.md)
2. [pipelines](2_2_pipelines.md)
3. [middlewares](2_3_middlewares.md)
4. [actions a la Wordpress](2_4_wordpress_actions.md)
5. [filters a la Wordpress](2_5_wordpress_filters.md)
6. [custom runners](2_6_custom_processors.md)

The old "simple collection" processor no longer exists as a separate class; its behaviour (every callable receives the same arguments, results ignored) is now provided by `CallableAction` with `argumentsLimit: null`.

[Next: The Invokator registry](2_0_the_invokator.md)
