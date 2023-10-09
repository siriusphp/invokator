# Sirius StackRunner

[![Source Code](http://img.shields.io/badge/source-siriusphp/stackrunner-blue.svg)](https://github.com/siriusphp/stackrunner)
[![Latest Version](https://img.shields.io/packagist/v/siriusphp/stackrunner.svg)](https://github.com/siriusphp/stackrunner/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](https://github.com/siriusphp/stackrunner/blob/master/LICENSE)
[![Build Status](https://github.com/siriusphp/stackrunner/workflows/CI/badge.svg)](https://github.com/siriusphp/stackrunner/actions)
[![Total Downloads](https://img.shields.io/packagist/dt/siriusphp/stackrunner.svg)](https://packagist.org/packages/siriusphp/stackrunner)

Sirius StackRunner is a library that implements various patterns that execute a list of commands:

1. middlewares
2. pipelines
3. events
4. actions a la Wordpress
5. filters a la Wordpress

All of the above patterns have in common that they are actually a list of callables and they differ in the way they are executed in different ways. 

In the case of middlewares, the starting parameter (eg: a HTTP request) is passed from one callable to the next, each 
callable having the option to terminate with a result or call the next callable in the stack. 

In the case of pipelines, the result of each callable is passed to the next callable and the last callable will return the result of the pipeline

In the case of events, an `event` object is passed through each callable in the stack and each callable is independent.

## Elevator pitch

```php
use Sirius\StackRunner\Invoker;
use Sirius\StackRunner\Processors\PipelineProcessor;
use Sirius\StackRunner\Stack;

$container = app(); // your application DI container
$invoker = new Invoker($container)
$processor = new PipelineProcessor($invoker);

$stack = new Stack();
$stack->add('trim');
$stack->add('Str::toUppercase');
$stack->add(fn($value) => {             // anonymous function
    return $value . '!!!';
});
$stack->add('Logger@info');

$processor->process($stack, "  hello world  "); // returns `HELLO WORLD!!!`
```

## Where to next?

- [documentation](https://sirius.ro/php/sirius/stack_runner/)
- [changelog](CHANGELOG.md)

## Todo
- [] Implement resumable pipelines
- [] Document other use cases
