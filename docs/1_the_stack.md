---
title: What are stacks in Sirius\StackRunner?
---

# The callable stack

In the `Sirius\StackRunner` lingo a **stack** is a list of callables organized by priority. By default, the priority is determined by the order the callables are added to the stack. The callables are executed in the order of their priority.

A **callable** is something that can be executed directly or after being interpreted by the [invoker](2_the_invoker.md). For example the `Invoker` class that comes with this library can recognize and execute callables in the form of 
`SomeClass@someMethod`

Even though stacks may have different purposes (middleware, events etc), a stack is defined in a single way. 

Below it's an example for a stack designed to run as a pipeline that process a piece of text

```php
use Sirius\StackRunner\Stack;

$stack = new Stack();

// add a regular function to the stack
$stack->add('trim');

// add an anonymous function
$stack->add(function($str) {
   return 'hello ' . $str;
});

// add a static method to the stack
$stack->add('Str::toUpper');

// add an object method to the stack,
// object to be retrieved at runtime from the container
// the callable also has a priority of 100
$stack->add('SlackChannel@send', 100);

// add an object method to the stack
// with a specific priority to be executed
// before the callable that was registered above 
$stack->add([$logger, 'info'], 3);
```

Stacks have a factory method that let you replace the code above with

```php
use Sirius\StackRunner\Stack;

$stack = new Stack();
$stack->add('trim');
$stack->add(function($str) {
   return 'hello ' . $str;
});
$stack->add('Str::toUpper');
$stack->add('SlackChannel@send', 3);
$stack->add([$logger, 'info'], 3);
```

#### Callable priority

By default, all callables in a stack have priority **zero** and callables with the same priority are executed in the order they are added to the stack.

A callable with a higher priority will be executed before a callable with a lower priority.

## Executing a stack

The `Sirius\StackRunner` library comes with a few **stack locators** which are act as stack registries/repositories and stack executors.

```php
use Sirius\StackRunner\Locators\PipelineLocator;
use Sirius\StackRunner\Invoker;

// this is required for callables like "SomeClass@someMethod"
// and by callables that have dependencies
$invoker = new Invoker($yourChoiceOfDependencyInjectionContainer);
$locator = new PipelineLocator($invoker);

// execute the $stack created above as a pipeline with one parameter
$locator->processStack($stack, ' world '); 

// this will
// 1. create string `HELLO WORLD`,
// 2. Write an info message to the logger
// 3. send it to a SlackChannel
```

Each type of stack locator has its own quirks that you can learn on the next page.

[Next: The stack_locators](2_stack_locators.md)
