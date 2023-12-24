---
title: What are callable processors in Sirius\Invokator?
---

# The callable processors

Callable processors are objects that have 3 objectives:
1. To act as a registry for collections via `$processor->get('callables_identifier')`
2. To simplify adding callbacks to collections via `$processor->add('callables_identifier', $callable, $priority)` which is syntactic sugar for `$processor->get('callables_identifier')->add($callable, $priority)`
3. To process collections stored in the registry via `$processor->process('callables_identifier', $param_1, $param_2)`
4. To process callable collections constructed separately via `$processor->processCollection($previouslyConstructedCollection, $param_1, $param_2)` 

The processors depend on the [invoker](4_the_invoker.md) to actually execute the callbacks.

The `Sirius\Invokator` library comes with 5 callable processors/runners
1. [simple collection](2_1_simple_collection.md)
2. [pipelines](2_2_pipelines.md)
3. [middlewares](2_3_middlewares.md)
4. [actions a la Wordpress](2_4_wordpress_actions.md)
5. [filters a la Wordpress](2_5_wordpress_filters.md)
6. [custom processors](2_6_custom_processors.md)
