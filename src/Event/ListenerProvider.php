<?php

declare(strict_types=1);

namespace Sirius\Invokator\Event;

use Psr\EventDispatcher\ListenerProviderInterface;
use Sirius\Invokator\CallableCollection;
use function Sirius\Invokator\once;

class ListenerProvider implements ListenerProviderInterface, ListenerSubscriber
{
    /**
     * @var array<string, CallableCollection|iterable>
     */
    protected array $registry = []; // @phpstan-ignore-line

    /**
     * @return iterable|CallableCollection
     */
    public function getListenersForEvent(object $event): iterable // @phpstan-ignore-line
    {
        $eventName = get_class($event);
        if ($event instanceof HasEventName) {
            $eventName = $event->getEventName();
        }

        return $this->registry[$eventName] ?? new CallableCollection();
    }

    public function subscribeTo(string $eventName, mixed $callable, int $priority = 0): void
    {
        /** @var CallableCollection $stack */
        $stack = $this->registry[$eventName] ?? new CallableCollection();

        $stack->add($callable, $priority);

        $this->registry[$eventName] = $stack;
    }

    public function subscribeOnceTo(string $eventName, mixed $callable, int $priority = 0): void
    {
        /** @var CallableCollection $stack */
        $stack = $this->registry[$eventName] ?? new CallableCollection();

        $stack->add(once($callable), $priority);

        $this->registry[$eventName] = $stack;
    }
}
