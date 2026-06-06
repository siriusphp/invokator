<?php

declare(strict_types=1);

namespace Sirius\Invokator\Event;

class EventWithName implements HasEventName {

    public function getEventName(): string
    {
        return 'event_with_name';
    }
}
