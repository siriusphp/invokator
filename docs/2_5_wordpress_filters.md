---
title: Filters a la Wordpress
---

# Filters (a la Wordpress) locator

This locator is similar to the "Pipeline locator" with the difference that the additional parameters passed to the `process()` method are also passed to the other callbacks.

Just like the "actions locator" you can specify the number of arguments passed to the callbacks

#### Use case

```php
use Sirius\StackRunner\Invoker;
use Sirius\StackRunner\Locators\FiltersLocator;

$invoker = new Invoker($psr11Container);
$locator = new FiltersLocator($invoker);

$locator->add('the_title', 'add_category_name', 0, 2); // callback, priority, no of arguments passed 
$locator->add('the_title', 'add_site_name', 0, 2);

$locator->process('the_title', $postTitle, $postID);
```

**Attention!** The locator's `get()` and `add()` method return the Stack object, so you can't chain callables with arguments limit. For example the code below doesn't work as you might expect
```php
$locator->add('the_title', 'add_category_name', 0, 2) // this returns the stack
        ->add('add_site_name', 0, 2) // this won't place a limit on the arguments for the 'add_site_name' function
```

[Next: Custom callable locators](2_6_custom_locators.md)
