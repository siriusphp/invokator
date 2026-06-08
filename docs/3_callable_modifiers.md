---
title: What are callable modifiers in Sirius\Invokator?
---

# Callable modifiers

Callable modifiers are invokable classes that alter, at runtime, how the callables from a collection are actually being executed. 

Since a collection is composed of callables, the callable modifiers are actually callables as they implement the `__invoke()` method.

The modifiers are **composable** which means you can wrap them one on top of another. 

The `Sirius\Invokator` library comes with a bunch of modifiers that allow you to simplify how you compose your collection. The examples use function that create the actual modifiers.

## The "limit arguments" modifier

This modifier will limit the number of arguments passed to the callables. If your collection starts the execution with 5 arguments and the signature of a callable has only one parameter you have to use the `LimitArguments` modifier

```php
use function Sirius\Invokator\limit_arguments;
use Sirius\Invokator\Invoker;
use Sirius\Invokator\Callables\CallableAction;

$invoker = new Invoker($psr11Container);
$action = new CallableAction($invoker);

// argumentsLimit: null so each modifier receives all the arguments
$action->add(limit_arguments(function($param_1, $param2) {
              return 'something';
          }, 2), 0, null)
       ->add(limit_arguments('Service@method', 1), 0, null);

$action->run($param_1, $param_2, $param_3, $param_4);
```

Even though the runner will receive 4 arguments, the callables will only receive 2 and 1 arguments respectively.

This modifier is used by the [actions runner](2_4_wordpress_actions.md) and the [filters runner](2_5_wordpress_filters.md)

## The "once" modifier

This modifier will ensure that a callable from a collection is executed only once, even though the collection might be processed multiple times.

It is useful for an events system where you want a particular listener to be executed only once. 

**Attention!** The result of the first execution is stored in memory and returned on subsequent calls which might not be what you want.

```php
use function Sirius\Invokator\once;
$action = new CallableAction($invoker);
$action->add(once(function($param_1, $param2) {
            return $param_1 + $param2;
          }), 0, null);

$action->run(2, 3);  // this returns 5
$action->run(8, 7);  // this STILL returns 5
```

## The "wrap" modifier

This modifier wraps a callable inside a function that only receives one argument, that is the original callable.

This can be used to override how the callable is actually being executed by passing different arguments.

```php
use function Sirius\Invokator\wrap;
$action = new CallableAction($invoker);
$action->add(wrap('Service@method', function(callable $callable) use ($param_3, $param_4) {
              return $callable($param_3, $param_4);
          }), 0, null);

$action->run($param_1, $param_2);
```

The `Service@method` function will actually receive $param_3 and $param_4 as arguments instead of $param_1 and $param_2

## The "with arguments" modifier

This modifier can be used when you have a callable that has a specific signature, and you don't want to change its signature nor do you want to wrap it inside an anonymous function (eg: because you might need to serialize the collection)

```php
use function Sirius\Invokator\with_arguments;
use function Sirius\Invokator\ref;
use function Sirius\Invokator\arg;
$action = new CallableAction($invoker);
$action->add(with_arguments('Service@method', [arg(0), 'value', ref('SomeClass'), arg(1)]), 0, null);

$action->run($param_1, $param_2);
// 
```

This is the same as calling `Service@method($param_1, 'value', $container->get('SomeClass'), $param_2)`

## The "resolve" modifier

Some callables might have dependencies on other services, and you might not know them while you call them, or you might not want to be forced to use `ref()` them. In this case you can use the `resolve()` modifier.

The `resolve()` modifier works with the `arg()` and `ref()` utilities.

```php
use function Sirius\Invokator\resolve;
use function Sirius\Invokator\arg;

// Service@method($param_1, SomeClass $param_2, $param_3)
$action = new CallableAction($invoker);
$action->add(resolve('Service@method', ['param_1' => arg(0), 'param_3' => 20]), 0, null);

$action->run(10);
```

This will call `Service@method(10, $container->get('SomeClass'), 20)`

You will learn about the `arg()` and `ref()` functions on the [invoker](4_the_invoker.md) page

[Next: The callable invoker](4_the_invoker.md)
