<?php

namespace Sirius\Invokator\Event;

use Sirius\Invokator\TestCase;

require_once __DIR__ . '/EventWithName.php';
require_once __DIR__ . '/EventWithoutName.php';
require_once __DIR__ . '/StoppableEvent.php';

class DispatcherTest extends TestCase
{
    public function test_subscribers_are_executed_in_order()
    {
        $dispatcher = new Dispatcher(new ListenerProvider(), $this->getInvoker());
        $dispatcher->subscribeTo('event_with_name', function (object $event) {
            static::$results[] = 'subscriber 1';
        });
        $dispatcher->subscribeTo('event_with_name', function (object $event) {
            static::$results[] = 'subscriber 2';
        });
        $dispatcher->subscribeTo('event_with_name', function (object $event) {
            static::$results[] = 'subscriber 3';
        });

        $dispatcher->dispatch(new EventWithName());

        $this->assertSame([
            'subscriber 1',
            'subscriber 2',
            'subscriber 3',
        ], static::$results);
    }

    public function test_stoppable_events()
    {
        $dispatcher = new Dispatcher(new ListenerProvider(), $this->getInvoker());
        $dispatcher->subscribeTo(StoppableEvent::class, function (object $event) {
            static::$results[] = 'subscriber 1';
        });
        $dispatcher->subscribeTo(StoppableEvent::class, function (object $event) {
            static::$results[] = 'subscriber 2';
            $event->stopPropagation();
        });
        $dispatcher->subscribeTo(StoppableEvent::class, function (object $event) {
            static::$results[] = 'subscriber 3';
        });

        $dispatcher->dispatch(new StoppableEvent());

        $this->assertSame([
            'subscriber 1',
            'subscriber 2',
        ], static::$results);
    }

    public function test_once_subscribers()
    {
        $dispatcher = new Dispatcher(new ListenerProvider(), $this->getInvoker());
        $dispatcher->subscribeOnceTo(EventWithoutName::class, function (object $event) {
            static::$results[] = 'once subscriber';
        });

        $dispatcher->dispatch(new EventWithoutName());
        $dispatcher->dispatch(new EventWithoutName());
        $dispatcher->dispatch(new EventWithoutName());
        $dispatcher->dispatch(new EventWithoutName());

        $this->assertSame([
            'once subscriber',
        ], static::$results);
    }
}
