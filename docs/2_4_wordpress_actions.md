---
title: Actions a la Wordpress
---

# Actions (a la Wordpress)

A `CallableAction` is similar to the simple collection behaviour with the difference that you also specify, per callable, a limit for the arguments passed to it (the default being `1`, the Wordpress action convention).

This means that the callables do not have to have the same signature. It is just a convenience as the same result could be been achieved using the ['limit_arguments' modifier](3_callable_modifiers.md). Passing `argumentsLimit: null` passes every argument unchanged (the old "simple collection" behaviour).

#### Use case

Using the `Invokator` registry:

```php
$invokator->action('save_post')
          ->add('validate_taxonomies', 0, 2)  // callback, priority, number of arguments passed
          ->add('validate_acf_fields', 1, 2)
          ->add('check_permissions', 10, 1);

$invokator->action('save_post')->run($postID, $wpPost, $update);
```

or standalone:

```php
use Sirius\Invokator\Invoker;
use Sirius\Invokator\Callables\CallableAction;

$invoker = new Invoker($psr11Container);

$action = new CallableAction($invoker);
$action->add('validate_taxonomies', 0, 2)
       ->add('validate_acf_fields', 1, 2)
       ->add('check_permissions', 10, 1);

$action->run($postID, $wpPost, $update);
```

Chaining works as expected: `add()` returns the action itself and each call keeps its own argument limit, so the example above limits `validate_taxonomies` and `validate_acf_fields` to 2 arguments and `check_permissions` to 1.

[Next: Filters a la Wordpress](2_5_wordpress_filters.md)
