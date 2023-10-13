<?php

namespace Sirius\Invokator\Event;

use Psr\EventDispatcher\StoppableEventInterface;

class StoppableEvent implements StoppableEventInterface
{
    use Stoppable;
}
