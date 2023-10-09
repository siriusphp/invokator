---
title: Sirius\StackRunner. PHP library to control your application flow using patterns
---

#Sirius Stack Runner

[![Source Code](http://img.shields.io/badge/source-siriusphp/stackrunner-blue.svg)](https://github.com/siriusphp/stackrunner)
[![Latest Version](https://img.shields.io/packagist/v/siriusphp/stack_runner.svg)](https://github.com/siriusphp/stackrunner/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](https://github.com/siriusphp/stackrunner/blob/master/LICENSE)
[![Build Status](https://github.com/siriusphp/stackrunner/workflows/CI/badge.svg)](https://github.com/siriusphp/stackrunner/actions)
[![Total Downloads](https://img.shields.io/packagist/dt/siriusphp/stackrunner.svg)](https://packagist.org/packages/siriusphp/stackrunner)

Sirius StackRunner is a library that implements various patterns that execute a list of commands:

1. middlewares
2. pipelines
3. events
4. actions and filters a la Wordpress

All of the above patterns have in common that they are actually a list of callables that have to be executed in different ways.

In the case of **middlewares**, the starting parameter (eg: a HTTP request) is passed from one callable to the next, each
callable having the option to terminate with a result or call the next callable in the stack.

In the case of **pipelines**, the result of each callable is passed to the next callable and the last callable will return the result of the pipeline. 

The Wordpress' **filter hooks** work similarly to pipelines with the difference that there is a "starting value" which can be modified by the callables and all the parameters are being passed to all the callables.

In the case of **events**, an `event` object is passed through each callable in the stack and each callable is independent (it doesn't receive the result from the previous item in the stack). 

The Wordpress' **action hooks** are similar to events as the callables are not influenced by each other

### Install using Composer

Sirius\StackRunner is available on [Packagist](https://packagist.org/packages/siriusphp/stackrunner). To install it run

```
composer require siriusphp/stackrunner
```

[Next: The callable stack](1_the_stack.md)
