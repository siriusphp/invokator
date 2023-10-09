<?php

declare(strict_types=1);

namespace Sirius\StackRunner\Event;

interface HasEventName
{
    public function getEventName(): string;
}
