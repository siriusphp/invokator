<?php

declare(strict_types=1);

namespace Sirius\Invokator\Event;

use Psr\EventDispatcher\StoppableEventInterface;

class StoppableEvent implements StoppableEventInterface
{
    use Stoppable;
}
