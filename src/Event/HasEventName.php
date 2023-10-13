<?php

declare(strict_types=1);

namespace Sirius\Invokator\Event;

interface HasEventName
{
    public function getEventName(): string;
}
