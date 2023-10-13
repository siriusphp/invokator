---
title: Actions a la Wordpress
---

# Actions (a la Wordpress)

This processor is similar to the "Simple callables processor" with the difference that you also have to specify a limit for the arguments passed to each callable.

This means that the callables do not have to have the same signature as for the SimpleCallables processor. This processor is just a convenience as the same result could be been achieved using the ['limit_arguments' modifier](4_callable_modifiers.md)

#### Use case

```php
use Sirius\Invokator\Invoker;
use Sirius\Invokator\Processors\ActionsProcessor;

$invoker = new Invoker($psr11Container);
$processor = new ActionsProcessor($invoker);

$processor->add('save_post', 'validate_taxonomies', 0, 2); // callback, priority, no of arguments passed 
$processor->add('save_post', 'validate_acf_fields', 0, 2);
$processor->add('save_post', 'check_permissions', 10, 1);

$processor->process('save_post', $postID, $wpPost, $update);
```

**Attention!** The processor's `get()` and `add()` method return the Stack object, so you can't chain callables with arguments limit. For example the code below doesn't work as you might expect
```php
$processor->add('save_post', 'validate_taxonomies', 0, 2) // this returns the collection
          ->add('validate_acf_fields', 0, 2); // this won't place a limit on the arguments for the 'validate_acf_fields' function
```

[Next: Filters a la Wordpress](2_5_wordpress_filters.md)
