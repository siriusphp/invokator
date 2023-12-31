---
title: What are callable collection in Sirius\Invokator?
---

# The callable collections

In the `Sirius\Invokator` items in the callable collections are organized by priority. By default, the priority is determined by the order the callables are added to the collection. The callables are executed in the order of their priority.

A **callable** is something that can be executed directly or after being interpreted by the [invoker](4_the_invoker.md). 
For example the `Invoker` class that comes with this library can recognize and execute callables in the form of `SomeClass@someMethod`

Even though callable collections may have different purposes (middleware, events, etc.), a collection is defined in a single way. 

Below it's an example for a collection designed to run as a pipeline that process a piece of text

```php
use Sirius\Invokator\CallableCollection;

$callables = new CallableCollection();

// add a regular function to the collection
$callables->add('trim');

// add an anonymous function
$callables->add(function($str) {
   return 'hello ' . $str;
});

// add a static method to the collection
$callables->add('Str::toUpper');

// add an object method to the collection,
// object to be retrieved at runtime from the container
// the callable also has a priority of -100
$callables->add('SlackChannel@send', -100);

// add an object method to the collection
// with a specific priority to be executed
// before the callable that was registered above 
$callables->add([$logger, 'info'], -3);
```

#### Callable priority

By default, all callables in a collection have priority **zero** and callables with the same priority are executed in the order they are added to the collection.

A callable with a higher priority will be executed before a callable with a lower priority.

## Executing a collection of callables

The `Sirius\Invokator` library comes with a few **collection processors** which are act as collection registries/repositories and collection executors.

```php
use Sirius\Invokator\Processors\PipelineProcessor;
use Sirius\Invokator\Invoker;

// this is required for callables like "SomeClass@someMethod"
// and by callables that have dependencies
$invoker = new Invoker($yourChoiceOfDependencyInjectionContainer);
$processor = new PipelineProcessor($invoker);

// execute the collection created above as a pipeline with one parameter
$processor->processCollection($callables, ' world '); 

// this will
// 1. trim the parameter => `world`
// 2. concatenate with "hello " => `hello world`
// 3. make the string uppercase => `HELLO WORLD`,
// 4. write the string as an info message to the logger
// 5. send the string to a SlackChannel
```

Each type of callables processor has its own quirks that you can learn on the next page.

[Next: The callable_processors](2_callable_processors.md)
