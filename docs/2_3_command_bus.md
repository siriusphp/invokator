---
title: Command bus implementation
---

# Command bus implementation

The `Sirius\Invokator` library comes with an implementation of the command bus pattern.

The command bus pattern is similar to the event pattern; a command is similar to an event (it's a message to be passed to a handler) and the command handler is similar to the event handler.

The difference is that there is only one command handler per command.

The implementation of this pattern in the `Sirius\Invokator` library has the following characteristics/defaults:

1. The `PurchaseProductCommand` command class is automatically linked to the `PurchaseProductHandler` in the same namespace. This happens unless you specify a handler via the `register()`/`handledBy()` method
2. The handler class has to implement the method `handle($command)` or `__invoke($command)`
3. The processing of the command can be extended via middlewares — under the hood the bus routes the command through a [`CallableMiddleware`](2_3_middlewares.md) stack to the handler

#### Use case

Using the `Invokator` registry:

```php
// The handler is auto-discovered: CreateProductCommand -> CreateProductHandler
$invokator->handle(new CreateProductCommand(/* ... */));
```

or standalone:

```php
use Sirius\Invokator\Invoker;
use Sirius\Invokator\Callables\CommandBus;

$invoker = new Invoker($psr11Container);
$bus = new CommandBus($invoker);

$bus->handle(new CreateProductCommand(/* ... */));
```

#### Registering a handler

By default `XxxCommand` is linked to `XxxHandler` in the same namespace, but you can bind a specific handler — including "un-orthodox" ones:

```php
$invokator->command(CreateProductCommand::class)->handledBy(CreateProductHandler::class);

// standalone, via the bus' register() method
$bus->register(CreateProductCommand::class, CreateProductHandler::class);
$bus->register(CreateProductCommand::class, function(CreateProductCommand $command) {
    // whatever
});
$bus->register(CreateProductCommand::class, 'SomeClass::staticMethod');
$bus->register(CreateProductCommand::class, 'SomeClass@execute');
```

#### Add middleware to commands

The processing of a command can be wrapped in middlewares, added at any point in time, before or after registering the command handler:

```php
$invokator->command(CreateProductCommand::class)
          ->add('CommandMiddleware@execute', 100 /* priority (optional) */);

// standalone, via the bus' addMiddleware() method
$bus->addMiddleware(CreateProductCommand::class, 'CommandMiddleware@execute', 100);
```

[Next: Actions a la Wordpress](2_4_wordpress_actions.md)
