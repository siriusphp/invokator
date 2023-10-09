<?php

declare(strict_types=1);

namespace Sirius\StackRunner\Event;

use Psr\EventDispatcher\ListenerProviderInterface;
use Sirius\StackRunner\Stack;
use function Sirius\StackRunner\once;

class ListenerProvider implements ListenerProviderInterface, ListenerSubscriber
{
    /**
     * @var array<string, Stack|iterable>
     */
    protected array $registry = []; // @phpstan-ignore-line

    /**
     * @return iterable|Stack
     */
    public function getListenersForEvent(object $event): iterable // @phpstan-ignore-line
    {
        $eventName = get_class($event);
        if ($event instanceof HasEventName) {
            $eventName = $event->getEventName();
        }

        return $this->registry[$eventName] ?? new Stack();
    }

    public function subscribeTo(string $eventName, mixed $callable, int $priority = 0): void
    {
        /** @var Stack $stack */
        $stack = $this->registry[$eventName] ?? new Stack();

        $stack->add($callable, $priority);

        $this->registry[$eventName] = $stack;
    }

    public function subscribeOnceTo(string $eventName, mixed $callable, int $priority = 0): void
    {
        /** @var Stack $stack */
        $stack = $this->registry[$eventName] ?? new Stack();

        $stack->add(once($callable), $priority);

        $this->registry[$eventName] = $stack;
    }
}
