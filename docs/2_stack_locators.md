---
title: What are stack locators in Sirius\StackRunner?
---

# The stack locators

Stack locators are objects that have 3 objectives:
1. To act as a registry for stacks via `$locator->get('stack_identifier')`
2. To simplify adding callbacks to stacks via `$locator->add('stack_identifier', $callable, $priority)` which is syntactic sugar for `$locator->get('stack_identifier')->add($callable, $priority)`
3. To process stacks stored in the registry via `$locator->process('stack_identifier', $param_1, $param_2)`
4. To process stacks constructed separately via `$locator->processStack($previouslyConstructedStack, $param_1, $param_2)` 

The stack locators depend on the [invoker](3_the_invoker.md) to actually execute the callbacks.

The `Sirius\StackRunner` library comes with 5 stack locators/runners

## The simple stack locator

This locator has the following characteristics:
1. All the parameters are passed down to each of the callables as which means all the callables should have the same signature (although this restriction can be by-passed with **modifiers**)
2. The values returned by the callables are ignored

#### Use case: Reporting/logging stack

```php
use Sirius\StackRunner\Invoker;
use Sirius\StackRunner\Locators\SimpleStackLocator;

$invoker = new Invoker($psr11Container);
$locator = new SimpleStackLocator($invoker);

$locator->add('log', 'FileLogger@log') // this returns the Stack
        ->add('SlackNotification@send')
        ->add('TextNotification@send')

$locator->process('log', $severity, $message, $context);
```

## The pipeline locator

This locator has the following characteristics:
1. The parameters are passed the first callable
2. The value returned by each callable is the first parameter passed to the next callable
3. All the callables are called in sequence

#### Use case

```php
use Sirius\StackRunner\Invoker;
use Sirius\StackRunner\Locators\PipelineLocator;

$invoker = new Invoker($psr11Container);
$locator = new PipelineLocator($invoker);

$locator->get('tax_report')
        ->add('ImportCsv@taxReport') // this receives a DTO with a file and a user ID, imports it into a table and returns a DTO with the table name and user ID
        ->add('GenerateTaxReport@compileExcelFile') // this receives the DTO returned by the previous callable, returns a DTO with the name of the XLS file and user ID
        ->add('NotifyReportReady@notifyTaxReport') // this receive the DTO from the previous callable and sends an email

$locator->process('tax_report', new TaxReportDTO('path_to_csv_file', 'user_id') );
```

## The middleware locator

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

## Actions (a la Wordpress) locator

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

## Filters (a la Wordpress) locator

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


## Custom locator

You can easily build your own custom locator by extending the `SimpleStackSelector` or starting from scratch as the API for a stack locator is very simple. You only need to implement the `processStack()` method.

One use case would be a pipeline where all the callbacks receive the same arguments and where the result of a callback becomes the first argument in the list. Similar to the "Filters locator" but without have to specify the limit for the arguments.

[Next: Callable modifiers](3_callable_modifiers.md)
