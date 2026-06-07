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

A `CallableCollection` is the underlying queue. To actually execute the callables you build one of the **runnable callable stacks** that come with `Sirius\Invokator` (here a `CallablePipeline`) and add the callables to it. Each runner owns its own collection and is executed with a single `run(...)` call.

```php
use Sirius\Invokator\Callables\CallablePipeline;
use Sirius\Invokator\Invoker;

// this is required for callables like "SomeClass@someMethod"
// and by callables that have dependencies
$invoker = new Invoker($yourChoiceOfDependencyInjectionContainer);

$pipeline = new CallablePipeline($invoker);
$pipeline->add('trim')
         ->add(fn ($s) => 'hello ' . $s)
         ->add('Str::toUpper')
         ->add('SlackChannel@send', -100);

// execute the pipeline with one parameter
$pipeline->run(' world ');

// this will
// 1. trim the parameter => `world`
// 2. concatenate with "hello " => `hello world`
// 3. make the string uppercase => `HELLO WORLD`,
// 4. send the string to a SlackChannel
```

Each type of callable runner has its own quirks that you can learn on the next page.

[Next: The callable_processors](2_callable_processors.md)
