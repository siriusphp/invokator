---
title: Simple stacks
---

# The simple stack locator

This locator has the following characteristics:
1. All the parameters are passed down to each of the callables as which means all the callables should have the same signature (although this restriction can be by-passed with **modifiers**)
2. The values returned by the callables are ignored

#### Use case: Reporting/logging stack

```php
use Sirius\StackRunner\Invoker;
use Sirius\StackRunner\Locators\SimpleStackLocator;

$invoker = new Invoker($psr11Container);
$locator = new SimpleStackLocator($invoker);

$locator->add('log', 'FileLogger@log') // this returns the Stack
        ->add('SlackNotification@send')
        ->add('TextNotification@send')

$locator->process('log', $severity, $message, $context);
```

[Next: Pipelines](2_2_pipelines.md)
