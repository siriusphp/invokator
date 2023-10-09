---
title: PSR-14 Event dispatcher implementation
---

# PSR-14 Event dispatcher implementation

The `Sirius\StackRunner` library comes with an implementation of the [PSR-14 Event Dispatcher](https://www.php-fig.org/psr/psr-14/).

```php
use Sirius\StackRunner\Invoker;
use Sirius\StackRunner\Event\Dispatcher;
use Sirius\StackRunner\Event\ListenerProvider;

$invoker = new Invoker($psr11Container);
$listenerProvider = new ListenerProvider();
$dispatcher = new Dispatcher($listenerProvider, $invoker);

// event name, listener, priority
$listenerProvider->subscribeTo(Event::class, 'some_callable', 0);
$listenerProvider->subscribeOnceTo(Event::class, 'some_callable', 0);

// if you use the Sirius\StackRunner\Event\ListenerProvider
// the same results as above can also be achieved with
$dispatcher->subscribeTo(Event::class, 'some_callable', 0);
$dispatcher->subscribeOnceTo(Event::class, 'some_callable', 0);
```

### Named events

If you want to identify the events by something other than the class name you can make the event classes implement the `HasEventname` interface

```php
use Sirius\StackRunner\Event\HasEventName;

class EventWithName implements HasEventName {
    public function getEventName() : string{
        return 'event_name';
    }
}
```

and then you can do something like

```php
$listenerProvider->subscribeTo('event_name', 'some_callable');
// later on
$dispatcher->dispatch(new EventWithName());
```

### Stoppable events

If you want some events to be able to stop the execution of the rest of the callables in the stack you can add the `Stoppable` trait to your event classes

```php
use Sirius\StackRunner\Event\Stoppable;

class StoppableEvent {
    use Stoppable;
}
```

and then you can do something like

```php
$listenerProvider->subscribeTo(StoppableEvent::class, function(object $event) {
    $event->stopPropagation();
});
// the subsequent callables won't be executed
$listenerProvider->subscribeTo(StoppableEvent::class, 'some_callable');
```

[Next: Actions a la Wordpress](2_4_wordpress_actions.md)