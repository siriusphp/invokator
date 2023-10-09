<?php

namespace Sirius\StackRunner\Event;

use Psr\EventDispatcher\StoppableEventInterface;

class StoppableEvent implements StoppableEventInterface
{
    use Stoppable;
}
