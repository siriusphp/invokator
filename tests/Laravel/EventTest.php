<?php

declare(strict_types=1);

namespace Sirius\Invokator\Tests\Laravel;

use Sirius\Invokator\Laravel\Facades\Invokator;
use Sirius\Invokator\Tests\Laravel\Fixtures\SampleEvent;
use Sirius\Invokator\Tests\Laravel\Fixtures\StoppableSampleEvent;

class EventTest extends TestCase
{
    public function test_subscribe_and_dispatch_an_event_object(): void
    {
        $seen = [];
        Invokator::event(SampleEvent::class)->add(function (SampleEvent $event) use (&$seen): void {
            $seen[] = $event->payload;
        });

        $returned = Invokator::dispatch(new SampleEvent('hello'));

        $this->assertInstanceOf(SampleEvent::class, $returned);
        $this->assertSame('hello', $returned->payload);
        $this->assertSame(['hello'], $seen);
    }

    public function test_do_event_helper_dispatches(): void
    {
        $seen = [];
        Invokator::event(SampleEvent::class)->add(function (SampleEvent $event) use (&$seen): void {
            $seen[] = $event->payload;
        });

        do_event(new SampleEvent('from-helper'));

        $this->assertSame(['from-helper'], $seen);
    }

    public function test_stoppable_event_halts_propagation(): void
    {
        $order = [];
        Invokator::event(StoppableSampleEvent::class)->add(function () use (&$order): void {
            $order[] = 1;
        });
        Invokator::event(StoppableSampleEvent::class)->add(function (StoppableSampleEvent $event) use (&$order): void {
            $order[] = 2;
            $event->stopPropagation();
        });
        Invokator::event(StoppableSampleEvent::class)->add(function () use (&$order): void {
            $order[] = 3;
        });

        Invokator::dispatch(new StoppableSampleEvent());

        $this->assertSame([1, 2], $order);
    }
}
