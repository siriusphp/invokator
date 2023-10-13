---
title: Simple callable collections
---

# The simple processor

This processor has the following characteristics:
1. All the parameters are passed down to each of the callables. This means all the callables should have the same signature (although this restriction can be by-passed with **modifiers**)
2. The values returned by the callables are ignored

#### Use case: Reporting/logging stack

```php
use Sirius\Invokator\Invoker;
use Sirius\Invokator\Processors\SimpleCallablesProcessor;

$invoker = new Invoker($psr11Container);
$processor = new SimpleCallablesProcessor($invoker);

$processor->add('log', 'FileLogger@log') // this returns the Stack
          ->add('SlackNotification@send')
          ->add('TextNotification@send');

$processor->process('log', $severity, $message, $context);
```

[Next: Pipelines](2_2_pipelines.md)
