---
title: What is the callable invoker in Sirius\StackRunner?
---

# The callable invoker

On its own, PHP recognizes a few callables based on how they are referenced:

1. a string that references a function, like `trim` or `SomePackage\someFunction`
2. a string that references a static method of a class, like `Str::ucwords`
3. an array that has an object as the first element and the method as the second element, like `[$someObj, 'method']`
4. an anonymous function, like `function($number) { return $number + 5}`
5. an instance of an invokable class (i.e. a class that has an `__invoke` method)

However, in the context of modern development these options are not enough. For this reason the `Sirius\StackRunner` library comes with an `Invoker` that can handle:

1. Callables in the format of `SomeClass@someMethod` 
2. Callables in the format of `SomeClass` with the condition that the class is invokable

The `Invoker` depends on a PSR-11 Container Interface in order to retrieve the objects referenced in the callables. 

For this reason it is possible to use services registered in the container by their name. For example, if the container can retrieve an item with the name `mailer`, you can reference a callable like this: `mailer@send` 

## Special parameters

When executing a callback, the invoker object, with go over the arguments passed for the callback and will handle the following special types of parameters:

##### 1. instances of the `InvokerReference` class. 
This is for when you want to pass a parameter that is actually a reference to an item in the container. It is useful if you don't want to retrieve the item from the container until it is actually 
  needed. 

Such an instance is created using the `Sirius\StackRunner\ref($identifier)` function.

##### 2. instances of the `InvokerResult` class. 
This for when you want to use as an argument for a callable the result of a computationally expensive function. Such an instance is created using the `Sirius\StackRunner\result_of($callable, [$param_1, 
  $param_2])` function

##### 3. instances of the `ArgumentReference` class. 
This for when you want to use as an argument in a different position than the position that argument was passed on by the processor. Such an instance is created using the `Sirius\StackRunner\arg(2)` function. 

For an example, check the documentation for the ["with arguments" modifier](3_callable_modifiers.md)

## Extending the invoker


[Next: The simple stack runner](3_simple_runner.md)
