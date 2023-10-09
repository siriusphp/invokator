---
title: Actions a la Wordpress
---

# Actions (a la Wordpress) locator

This locator is similar to the "Simple stack locator" with the difference that you also have to specify a limit for the arguments passed to each callable.

This means that the callables do not have to have the same signature as for the Simple stack locator. This locator is just a convenience as the same result could be been achieved using the ['limit_arguments' modifier](4_callable_modifiers.md)

#### Use case

```php
use Sirius\StackRunner\Invoker;
use Sirius\StackRunner\Locators\ActionsLocator;

$invoker = new Invoker($psr11Container);
$locator = new ActionsLocator($invoker);

$locator->add('save_post', 'validate_taxonomies', 0, 2); // callback, priority, no of arguments passed 
$locator->add('save_post', 'validate_acf_fields', 0, 2);
$locator->add('save_post', 'check_permissions', 10, 1);

$locator->process('save_post', $postID, $wpPost, $update);
```

**Attention!** The locator's `get()` and `add()` method return the Stack object, so you can't chain callables with arguments limit. For example the code below doesn't work as you might expect
```php
$locator->add('save_post', 'validate_taxonomies', 0, 2) // this returns the stack
        ->add('validate_acf_fields', 0, 2) // this won't place a limit on the arguments for the 'validate_acf_fields' function
```

[Next: Filters a la Wordpress](2_2_pipelines.md)
