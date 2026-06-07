---
title: Simple callable collections
---

# The simple collection behaviour

The "simple" behaviour has the following characteristics:
1. All the parameters are passed down to each of the callables. This means all the callables should have the same signature (although this restriction can be by-passed with **modifiers**)
2. The values returned by the callables are ignored

There is no longer a dedicated `SimpleCallablesProcessor`. This behaviour is now provided by `CallableAction` with `argumentsLimit: null`, which passes **every** argument unchanged to each callable.

#### Use case: Reporting/logging

Using the `Invokator` registry:

```php
$invokator->action('log')
          ->add('FileLogger@log', 0, null) // priority, then argumentsLimit: null
          ->add('SlackNotification@send', 0, null)
          ->add('TextNotification@send', 0, null);

$invokator->action('log')->run($severity, $message, $context);
```

or standalone:

```php
use Sirius\Invokator\Invoker;
use Sirius\Invokator\Callables\CallableAction;

$invoker = new Invoker($psr11Container);

$action = new CallableAction($invoker);
$action->add('FileLogger@log', 0, null)
       ->add('SlackNotification@send', 0, null)
       ->add('TextNotification@send', 0, null);

$action->run($severity, $message, $context);
```

[Next: Pipelines](2_2_pipelines.md)
