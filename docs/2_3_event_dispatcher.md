---
title: PSR-14 Event dispatcher implementation
---

# PSR-14 Event dispatcher implementation

The `Sirius\Invokator` library comes with an implementation of the [PSR-14 Event Dispatcher](https://www.php-fig.org/psr/psr-14/).

#### Use case

Using the `Invokator` registry, `event()` returns a `CallableEvent` that wraps the dispatcher and is the unified way to subscribe and dispatch:

```php
// subscribe a listener (optionally a priority)
$invokator->event(Event::class)->add('some_callable');
// subscribe a listener that runs only once
$invokator->event(Event::class)->once('some_callable');

// dispatch the event (these are equivalent)
$invokator->dispatch(new Event());
$invokator->event(Event::class)->run(new Event());
```

or standalone, using the PSR-14 dispatcher directly:

```php
use Sirius\Invokator\Invoker;
use Sirius\Invokator\Event\Dispatcher;
use Sirius\Invokator\Event\ListenerProvider;

$invoker = new Invoker($psr11Container);
$listenerProvider = new ListenerProvider();
$dispatcher = new Dispatcher($listenerProvider, $invoker);

// event name, listener, priority
$listenerProvider->subscribeTo(Event::class, 'some_callable', 0);
$listenerProvider->subscribeOnceTo(Event::class, 'some_callable', 0);

// if you use the \Sirius\Invokator\Event\ListenerProvider
// the same results as above can also be achieved with
$dispatcher->subscribeTo(Event::class, 'some_callable', 0);
$dispatcher->subscribeOnceTo(Event::class, 'some_callable', 0);

// dispatch the event
$dispatcher->dispatch(new Event());
```

### Named events

If you want to identify the events by something other than the class name you can make the event classes implement the `HasEventname` interface

```php
use Sirius\Invokator\Event\HasEventName;

class EventWithName implements HasEventName {
    public function getEventName() : string{
        return 'event_name';
    }
}
```

and then you can do something like

```php
$invokator->event('event_name')->add('some_callable');
// later on
$invokator->dispatch(new EventWithName());
```

### Stoppable events

If you want some events to be able to stop the execution of the rest of the callables in the collection you can add the `Stoppable` trait to your event classes

```php
use Sirius\Invokator\Event\Stoppable;

class StoppableEvent {
    use Stoppable;
}
```

and then you can do something like

```php
$invokator->event(StoppableEvent::class)->add(function(object $event) {
    $event->stopPropagation();
});
// the subsequent callables won't be executed
$invokator->event(StoppableEvent::class)->add('some_callable');
```

[Next: Command bus](2_3_command_bus.md)
