---
title: Middlewares
---

# Middlewares

This processor has the following characteristics:
1. All the parameters are passed down to each of the callables as which means all the callables should have the same signature (although this restriction can be by-passed with **modifiers**)
2. The second to the last callables receive a `$next` as their last parameter which is a callable that continues the calls from the collection
3. Each callable may call `$next` or not

#### Use case

```php
use Sirius\Invokator\Invoker;
use Sirius\Invokator\Processors\MiddlewareProcessor;

$invoker = new Invoker($psr11Container);
$processor = new MiddlewareProcessor($invoker);

$processor->get('http_handler')
          ->add('CsrfCheckMiddleware') 
          ->add('TrimStringsMiddleware')
          ->add('AuthMiddleware')
          ->add('CacheMiddleware')
          ->add('RouterMiddleware');

$processor->process('http_handler', new HttpRequest);
```

While this example is for HTTP middleware, it does not implement the [PSR-15 middleware specifications](https://www.php-fig.org/psr/psr-15/) as it does not enforce their respective signatures. It would be up to your app to enforce those restrictions

The example above can easily handle both the single-pass and double-pass types of [HTTP middleware](https://www.php-fig.org/psr/psr-15/meta/)

[Next: Event dispatcher](2_3_event_dispatcher.md)
