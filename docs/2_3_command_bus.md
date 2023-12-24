---
title: PSR-14 Command bus implementation
---

# Command bus implementation

The `Sirius\Invokator` library comes with an implementation of the command bus pattern.

The command bus pattern is similar to the event pattern; a command is similar to an event (it's a message to be passed to a handler) and the command handler is similar to the event handler.

The difference is that there is only one command handler per command.

The implementation of this pattern in the `Sirius\Invokator` library has the following characteristics/defaults:

1. The `PurchaseProductCommand` command class is automatically linked to the `PurchaseProductHandler` in the same namespace. This happens unless you specify a handler via the `register()` method
2. The handler class has to implement the method `handle($command)` or `__invoke($command)`
3. The processing of the command can be extended via middlewares (the command bus is a special type of [middleware processor](2_3_middlewares.md))

```php
use Sirius\Invokator\Invoker;
use Sirius\Invokator\Processors\CommandBus;

$invoker = new Invoker($psr11Container);
$bus = new CommandBus($invoker);

// this is what happens by default, you can skip it
$bus->register(CreateProductCommand::class, CreateProductHandler::class); 

// you can register "un-orthodox" handlers
$bus->register(CreateProductCommand::class, function(CreateProductCommand $command) {
    // whatever
});
$bus->register(CreateProductCommand::class, 'SomeClass::staticMethod');
$bus->register(CreateProductCommand::class, 'SomeClass@execute');
```

#### Add middleware to commands

You can add middlewares at any point in time, before or after registering the command handlers.

```php
$bus->addMiddleware(CreateProductCommand::class, 'CommandMiddleware@execute', 100 /* priority (optional) */);
```

[Next: Actions a la Wordpress](2_4_wordpress_actions.md)
