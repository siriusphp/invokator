<?php

declare(strict_types=1);

namespace Sirius\Invokator\Tests\Laravel\Fixtures;

use Psr\EventDispatcher\StoppableEventInterface;
use Sirius\Invokator\Event\Stoppable;

class StoppableSampleEvent implements StoppableEventInterface
{
    use Stoppable;
}
