---
title: Middlewares
---

# The middleware locator

This locator has the following characteristics:
1. All the parameters are passed down to each of the callables as which means all the callables should have the same signature (although this restriction can be by-passed with **modifiers**)
2. The second to the last callables receive a `$next` as their last parameter which is a callable that continues the calls from the stack
3. Each callable may call `$next` or not

#### Use case

```php
use Sirius\StackRunner\Invoker;
use Sirius\StackRunner\Locators\MiddlewareLocator;

$invoker = new Invoker($psr11Container);
$locator = new MiddlewareLocator($invoker);

$locator->get('dispatcher')
        ->add('CsrfCheckMiddleware') 
        ->add('TrimStringsMiddleware')
        ->add('AuthMiddleware')
        ->add('CacheMiddleware')
        ->add('RouterMiddleware')

$locator->process('dispatcher', new HttpRequest);
```

While this example is for HTTP middleware, it does not implement the [PSR-15 middleware specifications](https://www.php-fig.org/psr/psr-15/) as it does not enforce their respective signatures. It would be up to your app to enforce those restrictions

The example above can easily handle both the single-pass and double-pass types of [HTTP middleware](https://www.php-fig.org/psr/psr-15/meta/)

[Next: Actions a la Wordpress](2_4_wordpress_actions.md)