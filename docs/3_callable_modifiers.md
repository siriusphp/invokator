---
title: What callable modifiers in Sirius\StackRunner?
---

# Callable modifiers

Callable modifiers are invokable classes that alter, at runtime, how the callables from a stack are actually being executed. 

Since a stack is composed of callables, the callable modifiers are actually callables as they implement the `__invoke()` method.

The modifiers are composable which means you can wrap them one on top of another.

The `Sirius\StackRunner` library comes with a bunch of modifiers that allow you to simplify how you compose your stack. The examples use function that create the actual modifiers.

## The "limit arguments" modifier

This modifier will limit the number of arguments passed to the callables. If your stack starts the execution with 5 arguments and the signature of a callable has only one parameter you have to use the `LimitArguments` modifier

```php
use function Sirius\StackRunner\limit_arguments;
use Sirius\StackRunner\Invoker;
use Sirius\StackRunner\Locators\SimpleStackLocator;

$invoker = new Invoker($psr11Container);
$locator = new SimpleStackLocator($invoker);

$locator->get('stack')
        ->add(limit_arguments(function($param_1, $param2) {
            return 'something'
        }, 2));
        ->add(limit_arguments('Service@method', 1));

$locator->process('stack', $param_1, $param_2, $param_3, $param_4);
```

This modifier is used by the "actions locator" and the "filters locator"

## The "once" modifier

This modifier will ensure that a callable from a stack is executed only once, even though the stack might be processed multiple times.

It is useful for an events system where you want a particular listener to be executed only once. 

**Atention!** The result of the first execution is stored in memory and returned on subsequent calls which might not be what you want.

```php
use function Sirius\StackRunner\once;
$locator->get('event')
        ->add(once(function($param_1, $param2) {
            return 'something'
        }, 2));
        ->add(limit_arguments('Service@method', 1));

$locator->process('event', $param_1, $param_2);
```

## The "wrap" modifier

This modifier wraps a callable inside a function that only receives one argument, that is the original callable.

This can be used to override how the callable is actually being executed by passing different arguments.

```php
use function Sirius\StackRunner\wrap;
$locator->get('stack')
        ->add(wrap('Service@method', function(callable $callable) use ($param_1, $param_2) {
            return $callable($param_1, $param_2);
        }, 2));

$locator->process('stack', $param_1, $param_2);
```

## The "with arguments" modifier

This modifier can be used when you have a callable that has a specific signature, and you don't want to wrap change its signature nor do you want to wrap it inside an anonymous function (eg: because you want to serialize the stack)

```php
use function Sirius\StackRunner\with_arguments;
use function Sirius\StackRunner\ref;
$locator->get('stack')
        ->add(with_arguments('Service@method', [ref(0), 'value', ref('SomeClass'), ref(1)]);

$locator->process('stack', $param_1, $param_2);

// This is the same as calling Service@method($param_1, 'value', $container->get('SomeClass'), $param_2)
```

The `ref()` function generates an `InvokerReference` (more on this on the next page) object which can be either
1. an **integer** in which case it refers to the index of the parameters passed to the stack process method. In this example above the `ref(0)` corresponds to the first parameter.
2. a **string** in which case it refers to an object from the container. In the example above the invoker will replace `rev('SomeClass')` with the value returned by `$container->get('SomeClass')`

[Next: The callable invoker](4_the_invoker.md)
