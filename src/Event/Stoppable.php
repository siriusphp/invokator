<?php

declare(strict_types=1);

namespace Sirius\Invokator\Event;

trait Stoppable
{
    protected bool $propagationStopped = false;

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }
}
