---
title: What are callable processors in Sirius\Invokator?
---

# The callable processors

Callable processors are objects that have 3 objectives:
1. To act as a registry for stacks via `$processor->get('stack_identifier')`
2. To simplify adding callbacks to stacks via `$processor->add('stack_identifier', $callable, $priority)` which is syntactic sugar for `$processor->get('stack_identifier')->add($callable, $priority)`
3. To process stacks stored in the registry via `$processor->process('stack_identifier', $param_1, $param_2)`
4. To process stacks constructed separately via `$processor->processCollection($previouslyConstructedStack, $param_1, $param_2)` 

The processors depend on the [invoker](3_the_invoker.md) to actually execute the callbacks.

The `Sirius\Invokator` library comes with 5 callable processors/runners
1. [simple collection](2_1_simple_collection.md)
2. [pipelines](2_2_pipelines.md)
3. [middlewares](2_3_middlewares.md)
4. [actions a la Wordpress](2_4_wordpress_actions.md)
5. [filters a la Wordpress](2_5_wordpress_filters.md)

You can also implement pretty easily your own [custom callables](2_6_custom_processors.md)
