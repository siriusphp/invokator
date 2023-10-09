---
title: Filters a la Wordpress
---

# Filters (a la Wordpress)

This processor is similar to the "Pipeline processor" with the difference that the additional parameters passed to the `process()` method are also passed to the other callbacks.

Just like the "actions processor" you can specify the number of arguments passed to the callbacks

#### Use case

```php
use Sirius\StackRunner\Invoker;
use Sirius\StackRunner\Processors\FiltersProcessor;

$invoker = new Invoker($psr11Container);
$processor = new FiltersProcessor($invoker);

$processor->add('the_title', 'add_category_name', 0, 2); // callback, priority, no of arguments passed 
$processor->add('the_title', 'add_site_name', 0, 2);

$processor->process('the_title', $postTitle, $postID);
```

**Attention!** The processor's `get()` and `add()` method return the Stack object, so you can't chain callables with arguments limit. For example the code below doesn't work as you might expect
```php
$processor->add('the_title', 'add_category_name', 0, 2) // this returns the stack
        ->add('add_site_name', 0, 2) // this won't place a limit on the arguments for the 'add_site_name' function
```

[Next: Custom callable processors](2_6_custom_processors.md)
