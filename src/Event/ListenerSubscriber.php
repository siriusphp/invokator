<?php

declare(strict_types=1);

namespace Sirius\StackRunner\Event;

interface ListenerSubscriber
{
    public function subscribeTo(string $eventName, mixed $callable, int $priority = 0): void;

    public function subscribeOnceTo(string $eventName, mixed $callable, int $priority = 0): void;
}
