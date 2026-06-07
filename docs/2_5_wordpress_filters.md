---
title: Filters a la Wordpress
---

# Filters (a la Wordpress)

A `CallableFilter` is similar to a pipeline with the difference that only the **first** argument is threaded through the callables while the additional parameters passed to `run()` are also passed (as context) to each callback.

Just like the action runner you specify, per callable, the number of arguments passed to it (the default being `1`).

#### Use case

Using the `Invokator` registry:

```php
$invokator->filter('the_title')
          ->add('add_category_name', 0, 2)  // callback, priority, no of arguments passed
          ->add('add_site_name', 0, 2);

$invokator->filter('the_title')->run($postTitle, $postID);
```

or standalone:

```php
use Sirius\Invokator\Invoker;
use Sirius\Invokator\Callables\CallableFilter;

$invoker = new Invoker($psr11Container);

$filter = new CallableFilter($invoker);
$filter->add('add_category_name', 0, 2)
       ->add('add_site_name', 0, 2);

$filter->run($postTitle, $postID);
```

Chaining works as expected: `add()` returns the filter itself and each call keeps its own argument limit, so both `add_category_name` and `add_site_name` above receive 2 arguments.

[Next: Custom callable stacks](2_6_custom_processors.md)
