<?php

namespace Sirius\StackRunner\Event;

class EventWithName implements HasEventName {

    public function getEventName(): string
    {
        return 'event_with_name';
    }
}
