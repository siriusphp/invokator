<?php

declare(strict_types=1);

namespace Sirius\Invokator\Event;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use Sirius\Invokator\Invoker;
use Sirius\Invokator\CallableCollection;

class Dispatcher implements EventDispatcherInterface
{

    public function __construct(public ListenerProviderInterface $registry, public Invoker $invoker)
    {
    }

    public function dispatch(object $event): object
    {
        /** @var CallableCollection $stack */
        $stack = $this->registry->getListenersForEvent($event);

        /** @var mixed $callable */
        foreach ($stack as $callable) {
            $this->invoker->invoke($callable, $event);
            if ($event instanceof StoppableEventInterface &&
                $event->isPropagationStopped()) {
                break;
            }
        }

        return $event;
    }

    public function subscribeTo(string $eventName, mixed $callable, int $priority = 0): void
    {
        if ( ! $this->registry instanceof ListenerSubscriber) {
            throw new \LogicException(sprintf('Unable to subscribe listener because %s is not instace of %s',
                get_class($this->registry),
                ListenerSubscriber::class
            ));
        }

        $this->registry->subscribeTo($eventName, $callable, $priority);
    }

    public function subscribeOnceTo(string $eventName, mixed $callable, int $priority = 0): void
    {
        if ( ! $this->registry instanceof ListenerSubscriber) {
            throw new \LogicException(sprintf('Unable to subscribe listener because %s is not instace of %s',
                get_class($this->registry),
                ListenerSubscriber::class
            ));
        }

        $this->registry->subscribeOnceTo($eventName, $callable, $priority);
    }
}
